<html>
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
                            <a href="{$NEW_POST_LINK}" class="btn btn-primary mb-3">{$NEW_POST}</a>
                            <!-- Success and Error Alerts -->
                            {include file='includes/alerts.tpl'}
                            {if count($POST_LIST)}
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <tbody>
                                            <tr>
                                                <td><strong>{$GHOST_POST_NAME}</strong></td>
                                                <td><strong>{$POST_DATE}</strong></td>
                                                <td><strong>{$PUBLISHED}</strong></td>
                                                <td><strong>{$VIEWS}</strong></td>
                                                <td><strong>
                                                        <div class="float-md-right">{$GHOST_ACTION}</div>
                                                    </strong></td>
                                            </tr>
                                            {foreach from=$POST_LIST item=post}
                                                <tr>
                                                    <td>
                                                        <a class="btn btn-info btn-sm mr-1" href="{$post.view_link}" target="_blank"><i class="fas fa-external-link-alt"></i></a> <a href="{$post.view_link}" target="_blank">{$post.name}</a>
                                                    </td>
                                                    <td>{$post.date}</td>
                                                    <td>{if $post.published == "yes"}<i class="fa fa-check-circle text-success"></i>{else}<i class="fa fa-times-circle text-danger"></i>{/if}</td>
                                                    <td>{$post.views}</td>
                                                    <td>
                                                        <div class="float-md-right">
                                                            <a class="btn btn-warning btn-sm" href="{$post.edit_link}"><i
                                                                    class="fas fa-edit fa-fw"></i></a>
                                                            <button class="btn btn-danger btn-sm" type="button"
                                                                onclick="showDeleteModal('{$post.delete_link}')"><i
                                                                    class="fas fa-trash fa-fw"></i></button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            {/foreach}
                                        </tbody>
                                    </table>
                                </div>
                                {$PAGINATION}
                            {else} <br />{$NO_GHOST_POSTS} 
                            {/if}
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

        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{$ARE_YOU_SURE}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        {$CONFIRM_DELETE_POST}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{$NO}</button>
                        <a href="#" id="deleteLink" class="btn btn-primary">{$YES}</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- End Wrapper -->
    </div>

    {include file='scripts.tpl'}

    <script type="text/javascript">
        function showDeleteModal(id) {
            $('#deleteLink').attr('href', id);
            $('#deleteModal').modal().show();
        }
    </script>

</body>

</html>