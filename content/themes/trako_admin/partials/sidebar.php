<div class="navbar-default sidebar" role="navigation">
	<div class="sidebar-nav slimscrollsidebar">

		<div class="sidebar-head">
			<h3>
				<span class="fa-fw open-close" style="cursor: pointer">
					<i class="fa fa-times">
					</i>
				</span>
				<span class="hide-menu">
					Navigation
				</span>
			</h3>
		</div>
		
		<ul class="nav" id="side-menu" style="padding: 60px 0 0;">
		<?php 
		if($sidebars !== NULL):
			foreach($sidebars as $sidebar)://echo print_r($sidebar);exit;
		?>
			<li>
				<a href="<?=strtolower(underscore($sidebar->link)) ?>" class="waves-effect">
					<i class="<?=$sidebar->icon?> fa-fw" aria-hidden="true" data-icon="v">
					</i>
					<span class="hide-menu">
						<?=$sidebar->description?>
						<?php if($sidebar->child !== NULL): ?>
						<span class="fa arrow"></span>
						<span class="label label-rouded label-inverse pull-right"><?=count($sidebar->child)?></span>
						<?php endif?>
					</span>
				</a>
				<?php if($sidebar->child !== NULL):?>
				<ul class="nav nav-second-level collapse">
					<?php foreach($sidebar->child as $child):?>
                    <li> <a href="<?=$child->link?>"><i class="fa-fw"><?=$child->icon?></i><span class="hide-menu"><?=$child->description?></span></a> </li>
                	<?php endforeach;?>
                </ul>
                <?php endif?>
			</li>
			
		<?php		
			endforeach;
		endif;
		?>
		</ul>
	</div>
</div>







