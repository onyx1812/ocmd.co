<?php 
use CodeClouds\Unify\Service\Request;
use \CodeClouds\Unify\Service\Notice;
?>
<div class="unify-table-area dash-in">
    <div class="container-fluid unify-mid-heading p-0 mb-4">
        <div class="row">
            <div class="col-12">
                <div class="page-block-top-heading clearfix">
                    <h2 class="mid-heading">Tools</h2></div>
            </div>
        </div>
    </div>
    <div class="container-fluid unify-search p-0 mgbt-25 uni-shadow-box">
        <div class="row clearfix m-0">
            <div class="col-12 unify-top-search-left pr-0 pl-0">
                <div class="unify-white-menu clearfix">
                    <ul class="option-row">
                        <li class="<?php echo (Request::get('section') != $sections[0]) ? 'active' : ''; ?>" ><a href="admin.php?page=unify-tools" ><button type="button" class=" btn btn-link">Import &amp; Export</button> </a></li>
                        <li class="<?php echo (Request::get('section') == $sections[0]) ? 'active' : ''; ?>" ><a href="admin.php?page=unify-tools&section=<?php echo $sections[0] ?>" ><button type="button" class="btn btn-link">Product Mapping</button></a></li>
<!-- 
                        <li><button type="button" class=" btn btn-link ">Import &amp; Export</button></li> 
                        <li><button type="button" class=" btn btn-link ">Import &amp; Export</button></li>  -->
                    </ul>
                </div>
            </div>
        </div>
    </div>
	
	<?php
	
	if (!session_id()) { session_start(); }
	
	if (Notice::hasFlashMessage('unify_notification'))
	{
		include_once __DIR__ . '/Notice/notice.php';
	}

	if (Request::get('section') == $sections[0])
    {
        include_once __DIR__ . '/Tools/' . $sections[0] . '.php';
    }
    else
    {
        include_once __DIR__ . '/Tools/' . $sections[1] . '.php';
    }
    ?>
	
</div> 