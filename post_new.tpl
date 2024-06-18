{include file='header.tpl'}

<body id="page-top">

    <!-- Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        {include file='sidebar.tpl'}

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main content -->
            <div id="content">

                <!-- Topbar -->
                {include file='navbar.tpl'}

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">{$GHOST}</h1>
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{$PANEL_INDEX}">{$DASHBOARD}</a></li>
                            <li class="breadcrumb-item active">{$GHOST}</li>
                        </ol>
                    </div>

                    <!-- Update Notification -->
                    {include file='includes/update.tpl'}
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <h5 style="display:inline">{$NEW_POST}</h5>
                            <div class="float-md-right">
                                <a href="{$BACK_LINK}" class="btn btn-warning">{$BACK}</a>
                            </div>
                            <hr />
                            <!-- Success and Error Alerts -->
                            {include file='includes/alerts.tpl'}
                            <form action="" method="post">
                                <div class="form-group">
                                    <label for="InputPostName">{$GHOST_POST_NAME}</label>
                                    <input type="text" id="InputPostName" placeholder="{$GHOST_POST_NAME}"
name="ghost_post_name" class="form-control"{if isset($GHOST_POST_NAME_VALUE)} value="{$GHOST_POST_NAME_VALUE}"{/if}>
                                </div>
                                <div class="form-group">
                                    <label for="InputPostDate">{$GHOST_POST_DATE}</label>
                                    <br /><input type="datetime-local" id="InputPostDate" name="ghost_post_date"{if isset($GHOST_POST_DATE_VALUE)} value="{$GHOST_POST_DATE_VALUE}"{/if}>
                                </div>
                                <div class="form-group">
                                    <label for="InputPostImage">{$GHOST_POST_IMAGE}</label>
                                    <input type="text" id="InputPostImage" placeholder="{$GHOST_POST_IMAGE}"
                                        name="ghost_post_image" class="form-control"{if isset($GHOST_POST_IMAGE_VALUE)} value="{$GHOST_POST_IMAGE_VALUE}"{/if}>
                                </div>
                                <div class="form-group">
                                    <label for="InputPostRules">{$GHOST_POST_CONTENT}</label>
                                    <textarea name="ghost_post_content" rows="3" id="InputPostContent"
                                        class="form-control" placeholder="{$GHOST_POST_CONTENT}">{if isset($GHOST_POST_CONTENT_VALUE)}{$GHOST_POST_CONTENT_VALUE}{/if}</textarea>
                                </div>
                                <div class="form-group">
                                    <input type="hidden" name="token" value="{$TOKEN}">
                                    <input type="submit" class="btn btn-primary" value="{$SUBMIT}">
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Spacing -->
                    <div style="height:1rem;"></div>

                    <!-- End Page Content -->
                </div>

                <!-- End Main Content -->
            </div>

            {include file='footer.tpl'}

            <!-- End Content Wrapper -->
        </div>

        <!-- End Wrapper -->
    </div>

    {include file='scripts.tpl'}

</body>

</html>