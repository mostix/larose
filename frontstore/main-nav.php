 <div id="leo-mainnav">
    <div class="container">
      <div class="inner clearfix">
        <div class="row">
          <nav id="cavas_menu"  class="sf-contener leo-megamenu col-lg-12 col-md-12 col-sm-12 col-xs-2 col-sp-2">
            <div class="" role="navigation"> 
              <!-- Brand and toggle get grouped for better mobile display -->
              <div class="navbar-header clearfix">
                <button type="button" class="pull-left navbar-toggle btn-outline-inverse" data-toggle="collapse" data-target=".navbar-ex1-collapse"> 
                  <span class="sr-only">Toggle navigation</span> <span class="fa fa-bars"></span> 
                </button>
              </div>
              <!-- Collect the nav links, forms, and other content for toggling -->
              <div id="leo-top-menu" class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav megamenu">
                  <li class="home">
                    <a href="<?php if($current_lang == "bg") echo "/"; else echo "/en/home"?>" class="has-category">
                      <span class="menu-title"><?=$languages[$current_lang]['menu_home'];?></span>
                    </a>
                  </li>
                  <?php print_header_categories_menu($content_parent_id = 0, $number_of_hierarchy_levels = 3);?>
                  <?php print_header_menu($content_parent_id = 0, $content_hierarchy_level_start = 2, $number_of_hierarchy_levels = 4);?>
                </ul>
              </div>
            </div>
          </nav>
        </div>
      </div>
    </div>
  </div>