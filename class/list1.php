<!-- START LIST1-->
        <div id="content">
            <div class="box-style2">
                <div class="content">
                    <p>&#8249;&nbsp;&nbsp;<a href="/for-rent/0.html">Home</a></p>
<?php if($lic === true): ?>
                    <div class="links"> ( <a href="/postit.php">Post Your Own</a> ) </div>
<?php else: ?>
                    <div class="links"> ( <a href="/register.php">Register to Post Your Own Thing</a> ) </div>
<?php endif ?>
                </div>
            </div>
            <div class="box-style1">
                <div class="t"></div>
                <div class="list-heading">
                    <div>
                        <h2><?= $selectedCat->GetName(); ?></h2>
                    </div>
                    <div class="lists">Showing <?=$imgstart?> - <?=$imgstop?> of <?=$totalcount?> listings</div>
                </div>
                <div class="content">
<!-- END LIST1-->
