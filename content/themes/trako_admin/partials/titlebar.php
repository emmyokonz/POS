<div class="row bg-title">
    <div class="col-lg-4 col-md-5 col-sm-6 col-xs-12">
        <h4 class="page-title"><?php echo page_title($template['title']);?></h4>
        
    </div>
    <div class="col-lg-8 col-md-7 col-sm-6 col-xs-12">
        <button class="right-side-toggle waves-effect waves-light btn-danger btn btn-sm pull-right m-l-10">
        	<span>Quick Actions</span>
        </button>
        <?php if(config_item('top_menus')!==NULL):?>
			<?php foreach (config_item('top_menus') as $top_link): ?>
			<a href="<?=site_url($top_link['link'])?>">
				<span class="btn btn-sm btn-primary pull-right m-l-10"><?=$top_link['name']?></span>
			</a>
	        <?php endforeach ?>
        <?php endif ?>
    </div>
</div>