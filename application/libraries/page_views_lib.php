<?php

if (!defined('BASEPATH'))
exit('No direct script access allowed');

/**
* Description of track_visitor
*
* @author https://techcoderr.com
*/
class page_views_lib
{
	/*
	* Defines how many seconds a hit should be rememberd for. This prevents the
	* database from perpetually increasing in size. Thirty days (the default)
	* works well. If someone visits a page and comes back in a month, it will be
	* counted as another unique hit.
	*/

	private $HIT_OLD_AFTER_SECONDS ; // default: 10 days.

	/*
	* Don't count hits from search robots and crawlers.
	*/
	private $IGNORE_SEARCH_BOTS = TRUE;

	/*
	* Don't count the hit if the browser sends the DNT: 1 header.
	*/
	private $HONOR_DO_NOT_TRACK = FALSE;

	/*
	* ignore controllers e.g. 'admin'
	*/
	private $CONTROLLER_IGNORE_LIST = array(
		'admin'
	);

	/*
	* ignore ip address
	*/
	private $IP_IGNORE_LIST = array(
		'127.0.0.1'
	);

	/**
	*
	* @var _tables
	* list of tables
	*
	*/
	private $_tables = [];
	/**
	*
	* @var where cluase
	* array
	*
	*/
	private $_where = [];
	/**
	*
	* @var select clause
	* array
	*
	*/
	private $_select = [];
	private $_limit;
	private $_offset;
	private $_order;
	private $_order_by;
	private $_query_result = [];
	private $_errors = [];
	private $_messages = [];

	function __construct()
	{
		//        $this->ci = & get_instance();
		$this->load->library('user_agent');

		$this->_tables = config_item('tables');

		//hit time to live (ttl)
		$this->HIT_OLD_AFTER_SECONDS = $this->config->item('hit_time_to_live');

		log_message("info",'Page view library Loaded.');

		$this->purge();
	}


	/**
	* __get
	*
	* Enables the use of CI super-global without having to define an extra variable.
	*
	* I can't remember where I first saw this, so thank you if you are the original author. -Militis
	*
	* @param    string $var
	*
	* @return    mixed
	*/
	public function __get($var)
	{
		return get_instance()->$var;
	}


	function post_view_count($post_id)
	{
		if ($this->config->item('track_visitor') == TRUE)
		{
			$proceed = TRUE;
			if ($this->IGNORE_SEARCH_BOTS && $this->is_search_bot())
			{
				$proceed = FALSE;
			}
			if ($this->HONOR_DO_NOT_TRACK)
			{
				$proceed = FALSE;
			}
			foreach ($this->CONTROLLER_IGNORE_LIST as $controller)
			{
				if (strpos(trim($this->router->fetch_class()), $controller) !== FALSE)
				{
					$proceed = FALSE;
					break;
				}
			}
			if (in_array($this->input->server('REMOTE_ADDR'), $this->IP_IGNORE_LIST))
			{
				$proceed = FALSE;
			}

			if ($proceed === TRUE)
			{
				$this->log_counter($post_id);
			}
		}
	}

	private function log_counter($slug)
	{
		$this->session->set_tempdata('visitor_activity',md5($this->input->ip_address().$slug),60);
		$data = [
			'ip_address'=>$this->input->ip_address(),
			'referrer'=>$this->input->referrer(site_url($this->router->fetch_class()),true),
			'slug' => $slug,
			'user_agent' => $this->input->user_agent(),
			'visit_date' => time(),
			'visit_name' => $this->session->tempdata('visitor_activity')
		];
		$data = $this->app->_filter_data($this->_tables['visitors'] , $data);
		if ($this->is_unique_visitor($data['visit_name']) || $this->is_expired_visitor($data['visit_name'],$data['visit_date'])) {
			$this->db->trans_begin();

			$this->db->insert($this->_tables['visitors'],$data,true);
			if ($this->db->affected_rows()) {
				//lets update the post table.
				$this->update_post_counter($slug);
			}
			else
			{
				log_message('info','Visitor Not logged.');
				return FALSE;
			}
			if ($this->db->trans_status() == FALSE) {
				$this->db->trans_rollback();
				log_message('error','Visitors activity not logged.');
			}
			else
			{
				$this->db->trans_commit();
				return TRUE;
			}
		}

		return FALSE;
	}

	function update_post_counter($slug)
	{
		$this->db->where('id',$slug)->set('post_views ', 'post_views + 1', FALSE)->update($this->_tables['posts']);
	}

	private function is_expired_visitor($visit_name,$visit_date)
	{
		$unique = $this->db->where('visit_name',$visit_name)
		->where('visit_date <',($visit_date - $this->HIT_OLD_AFTER_SECONDS))
		->get($this->_tables['visitors'])
		->num_rows();

		if ($unique > 0)
		{
			return TRUE;
		}
		return(FALSE);
	}

	private function is_unique_visitor($visit_name)
	{
		$unique = $this->db->or_where('visit_name',$visit_name)
		->get($this->_tables['visitors'])
		->num_rows();

		if ($unique <= 0)
		{
			return TRUE;
		}
		return(FALSE);
	}

	function get_many()
	{
		if (isset($this->_select) && !empty($this->_select)) {
			foreach ($this->_select as $select) {
				$this->db->select($select);
			}

			$this->_select = [];
		}

		// run each where that was passed
		if (isset($this->_where) && !empty($this->_where)) {

			foreach ($this->_where as $where) {
				$this->db->where($where);
			}

			$this->_where = [];
		}
		// set the order
		if (isset($this->_order_by) && isset($this->_order)) {
			$this->db->order_by($this->_order_by, $this->_order);

			$this->_order = NULL;
			$this->_order_by = NULL;
		}

		if (isset($this->_limit) && isset($this->_offset)) {
			$this->db->limit($this->_limit, $this->_offset);

			$this->_limit = NULL;
			$this->_offset = NULL;
		}
		else
		if (isset($this->_limit)) {
			$this->db->limit($this->_limit);

			$this->_limit = NULL;
		}

		$this->_query_result = $this->db->get($this->_tables['visitors']);

		return $this;
	}
	
	function get($id = NULL)
	{
		if($id == null){
			$this->set_error(lang('error_visit_not_found'));
			return FALSE;
		}
		
		$this->limit(1);
		$this->order_by($this->_tables['visitors'].'.id','DESC');
		$this->where([$this->_tables['visitors'].'.id'=>$id]);
		
		$this->get_posts();
		
		return $this;
	}
	/**
	* @param string $by
	* @param string $order
	*
	* @return static
	*/
	public function order_by($by, $order = 'DESC')
	{

		$this->_order_by = $by;
		$this->_order = $order;

		return $this;
	}

	/**
	* @param int $limit
	*
	* @return static
	*/
	public function limit($limit)
	{
		$this->_limit = $limit;

		return $this;
	}

	/**
	* @param int $offset
	*
	* @return static
	*/

	public function offset($offset)
	{
		$this->_offset = $offset;

		return $this;
	}

	/**
	* @param array|string $select
	*
	* @return static
	*/
	public function select($select)
	{
		$this->_select[] = $select;

		return $this;
	}


	public function where($where , $value = NULL)
	{
		if (!is_array($where)) {
			$where = [$where => $value];
		}

		array_push($this->_where , $where);

		return $this;
	}

	/**
	* @return object|mixed
	*/
	public function row()
	{

		$row = $this->_query_result->row();

		return $row;
	}


	function result()
	{
		$result = $this->_query_result->result();
		return($result);
	}

	function result_array()
	{
		$result = $this->_query_result->result_array();
		return($result);
	}

	function num_rows()
	{
		$num_rows = $this->_query_result->num_rows();
		return $num_rows;
	}


	/**
	* check track_session
	*
	* @return	bool
	*/
	private function track_session()
	{
		return ($this->session->userdata('track_session') === TRUE ? TRUE : FALSE);
	}

	/**
	* check whether bot
	*
	* @return	bool
	*/
	private function is_search_bot()
	{
		// Of course, this is not perfect, but it at least catches the major
		// search engines that index most often.
		$spiders = array(
			"abot",
			"dbot",
			"ebot",
			"hbot",
			"kbot",
			"lbot",
			"mbot",
			"nbot",
			"obot",
			"pbot",
			"rbot",
			"sbot",
			"tbot",
			"vbot",
			"ybot",
			"zbot",
			"bot.",
			"bot/",
			"_bot",
			".bot",
			"/bot",
			"-bot",
			":bot",
			"(bot",
			"crawl",
			"slurp",
			"spider",
			"seek",
			"accoona",
			"acoon",
			"adressendeutschland",
			"ah-ha.com",
			"ahoy",
			"altavista",
			"ananzi",
			"anthill",
			"appie",
			"arachnophilia",
			"arale",
			"araneo",
			"aranha",
			"architext",
			"aretha",
			"arks",
			"asterias",
			"atlocal",
			"atn",
			"atomz",
			"augurfind",
			"backrub",
			"bannana_bot",
			"baypup",
			"bdfetch",
			"big brother",
			"biglotron",
			"bjaaland",
			"blackwidow",
			"blaiz",
			"blog",
			"blo.",
			"bloodhound",
			"boitho",
			"booch",
			"bradley",
			"butterfly",
			"calif",
			"cassandra",
			"ccubee",
			"cfetch",
			"charlotte",
			"churl",
			"cienciaficcion",
			"cmc",
			"collective",
			"comagent",
			"combine",
			"computingsite",
			"csci",
			"curl",
			"cusco",
			"daumoa",
			"deepindex",
			"delorie",
			"depspid",
			"deweb",
			"die blinde kuh",
			"digger",
			"ditto",
			"dmoz",
			"docomo",
			"download express",
			"dtaagent",
			"dwcp",
			"ebiness",
			"ebingbong",
			"e-collector",
			"ejupiter",
			"emacs-w3 search engine",
			"esther",
			"evliya celebi",
			"ezresult",
			"falcon",
			"felix ide",
			"ferret",
			"fetchrover",
			"fido",
			"findlinks",
			"fireball",
			"fish search",
			"fouineur",
			"funnelweb",
			"gazz",
			"gcreep",
			"genieknows",
			"getterroboplus",
			"geturl",
			"glx",
			"goforit",
			"golem",
			"grabber",
			"grapnel",
			"gralon",
			"griffon",
			"gromit",
			"grub",
			"gulliver",
			"hamahakki",
			"harvest",
			"havindex",
			"helix",
			"heritrix",
			"hku www octopus",
			"homerweb",
			"htdig",
			"html index",
			"html_analyzer",
			"htmlgobble",
			"hubater",
			"hyper-decontextualizer",
			"ia_archiver",
			"ibm_planetwide",
			"ichiro",
			"iconsurf",
			"iltrovatore",
			"image.kapsi.net",
			"imagelock",
			"incywincy",
			"indexer",
			"infobee",
			"informant",
			"ingrid",
			"inktomisearch.com",
			"inspector web",
			"intelliagent",
			"internet shinchakubin",
			"ip3000",
			"iron33",
			"israeli-search",
			"ivia",
			"jack",
			"jakarta",
			"javabee",
			"jetbot",
			"jumpstation",
			"katipo",
			"kdd-explorer",
			"kilroy",
			"knowledge",
			"kototoi",
			"kretrieve",
			"labelgrabber",
			"lachesis",
			"larbin",
			"legs",
			"libwww",
			"linkalarm",
			"link validator",
			"linkscan",
			"lockon",
			"lwp",
			"lycos",
			"magpie",
			"mantraagent",
			"mapoftheinternet",
			"marvin/",
			"mattie",
			"mediafox",
			"mediapartners",
			"mercator",
			"merzscope",
			"microsoft url control",
			"minirank",
			"miva",
			"mj12",
			"mnogosearch",
			"moget",
			"monster",
			"moose",
			"motor",
			"multitext",
			"muncher",
			"muscatferret",
			"mwd.search",
			"myweb",
			"najdi",
			"nameprotect",
			"nationaldirectory",
			"nazilla",
			"ncsa beta",
			"nec-meshexplorer",
			"nederland.zoek",
			"netcarta webmap engine",
			"netmechanic",
			"netresearchserver",
			"netscoop",
			"newscan-online",
			"nhse",
			"nokia6682/",
			"nomad",
			"noyona",
			"nutch",
			"nzexplorer",
			"objectssearch",
			"occam",
			"omni",
			"open text",
			"openfind",
			"openintelligencedata",
			"orb search",
			"osis-project",
			"pack rat",
			"pageboy",
			"pagebull",
			"page_verifier",
			"panscient",
			"parasite",
			"partnersite",
			"patric",
			"pear.",
			"pegasus",
			"peregrinator",
			"pgp key agent",
			"phantom",
			"phpdig",
			"picosearch",
			"piltdownman",
			"pimptrain",
			"pinpoint",
			"pioneer",
			"piranha",
			"plumtreewebaccessor",
			"pogodak",
			"poirot",
			"pompos",
			"poppelsdorf",
			"poppi",
			"popular iconoclast",
			"psycheclone",
			"publisher",
			"python",
			"rambler",
			"raven search",
			"roach",
			"road runner",
			"roadhouse",
			"robbie",
			"robofox",
			"robozilla",
			"rules",
			"salty",
			"sbider",
			"scooter",
			"scoutjet",
			"scrubby",
			"search.",
			"searchprocess",
			"semanticdiscovery",
			"senrigan",
			"sg-scout",
			"shai'hulud",
			"shark",
			"shopwiki",
			"sidewinder",
			"sift",
			"silk",
			"simmany",
			"site searcher",
			"site valet",
			"sitetech-rover",
			"skymob.com",
			"sleek",
			"smartwit",
			"sna-",
			"snappy",
			"snooper",
			"sohu",
			"speedfind",
			"sphere",
			"sphider",
			"spinner",
			"spyder",
			"steeler/",
			"suke",
			"suntek",
			"supersnooper",
			"surfnomore",
			"sven",
			"sygol",
			"szukacz",
			"tach black widow",
			"tarantula",
			"templeton",
			"/teoma",
			"t-h-u-n-d-e-r-s-t-o-n-e",
			"theophrastus",
			"titan",
			"titin",
			"tkwww",
			"toutatis",
			"t-rex",
			"tutorgig",
			"twiceler",
			"twisted",
			"ucsd",
			"udmsearch",
			"url check",
			"updated",
			"vagabondo",
			"valkyrie",
			"verticrawl",
			"victoria",
			"vision-search",
			"volcano",
			"voyager/",
			"voyager-hc",
			"w3c_validator",
			"w3m2",
			"w3mir",
			"walker",
			"wallpaper",
			"wanderer",
			"wauuu",
			"wavefire",
			"web core",
			"web hopper",
			"web wombat",
			"webbandit",
			"webcatcher",
			"webcopy",
			"webfoot",
			"weblayers",
			"weblinker",
			"weblog monitor",
			"webmirror",
			"webmonkey",
			"webquest",
			"webreaper",
			"websitepulse",
			"websnarf",
			"webstolperer",
			"webvac",
			"webwalk",
			"webwatch",
			"webwombat",
			"webzinger",
			"wget",
			"whizbang",
			"whowhere",
			"wild ferret",
			"worldlight",
			"wwwc",
			"wwwster",
			"xenu",
			"xget",
			"xift",
			"xirq",
			"yandex",
			"yanga",
			"yeti",
			"yodao",
			"zao/",
			"zippp",
			"zyborg"
		);

		$agent = strtolower($this->agent->agent_string());

		foreach ($spiders as $spider)
		{
			if (strpos($agent, $spider) !== FALSE)
			return TRUE;
		}

		return FALSE;
	}

	private function purge()
	{
		$this->db->where('visit_date < ' , (time() - config_item('vistors_hit_purge_time')));
		$this->db->delete($this->_tables['visitors']);
		return;
	}

}

/* End of file track_visitor.php */
/* Location: ./application/hooks/Track_Visitor.php */