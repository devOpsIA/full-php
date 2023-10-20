<div class="panel panel-default bg-inverse">
    <table class="table sheetTable">
        <tbody>
            <tr>
                <td style="width: 170px; height: 32px;" class="left-padding"><?php echo $this->lang->line('metadata_parent_folder'); ?>:</td>
                <td>
                    <a href="javascript:;" class="btn btn-sm purple-plum" onclick="commonFolderDataGrid('single', '', 'chooseParentFolder', this);">...</a>
                    <?php echo Form::hidden(array('name' => 'parentFolderId', 'value' => $this->row['PARENT_FOLDER_ID'])); ?> 
                    <span class="parent-folder-name"><?php echo $this->row['PARENT_FOLDER_NAME']; ?></span>
                </td>
            </tr>
        </tbody>
    </table>
</div>