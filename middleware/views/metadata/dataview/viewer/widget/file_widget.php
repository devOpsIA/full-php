<?php
if (isset($this->row['dataViewLayoutTypes']) && isset($this->row['dataViewLayoutTypes']['card_business'])) {
    $typeRow = $this->row['dataViewLayoutTypes']['card_business'];
?>
var filewidgetview_<?php echo $this->metaDataId; ?> = $.extend({}, $.fn.datagrid.defaults.view, {
    render: function (target, container, frozen) {
        var data = $.data(target, "datagrid");
        var rows = data.data.rows;
        var fields = $(target).datagrid("getColumnFields", frozen);
        var cls = "class=\"datagrid-row\"";
        var table = [], grouped = [], dgindex = 0;
        
            
        table.push('<div class="media-list-activity mg-b-20">');
        for (var i = 0; i < rows.length; i++) {
            table.push('<div class="media">');
                table.push(this.renderRow.call(this, target, fields, frozen, i, rows[i]));
            table.push('</div>');
        }
        table.push('</div>');

        $(container).html(table.join(''));
    },
    renderRow: function (target, fields, frozen, rowIndex, rowData) {
        var cc = [];
            <?php
            if (isset($typeRow['fields']['name1'])) {
                $name1 = strtolower($typeRow['fields']['name1']); ?>
                var name1Col = $(target).datagrid('getColumnOption', '<?php echo $name1; ?>');
                if (typeof name1Col !== 'undefined' && name1Col != null && name1Col.formatter) {
                    cc.push(name1Col.formatter(rowData.<?php echo $name1; ?>, rowData, rowIndex));
                } else {
                    cc.push(rowData.<?php echo $name1; ?>);
                }
            <?php
            } ?>

            cc.push('<div class="media-body">');
            <?php if (isset($typeRow['fields']['name2'])) {
                    $name2 = strtolower($typeRow['fields']['name2']);
                ?>
                cc.push('<h6 class="font-weight-bold">');
                    var name2Col = $(target).datagrid('getColumnOption', '<?php echo $name2; ?>');
                    if (typeof name2Col !== 'undefined' && name2Col != null && name2Col.formatter) {
                        cc.push(name2Col.formatter(rowData.<?php echo $name2; ?>, rowData, rowIndex));
                    } else {
                        cc.push(rowData.<?php echo $name2; ?>);
                    }
                cc.push('</h6>'); 

                <?php
            }
            if (isset($typeRow['fields']['name3'])) {
                $name3 = strtolower($typeRow['fields']['name3']);
            ?>
            cc.push('<span>');
                var name3Col = $(target).datagrid('getColumnOption', '<?php echo $name3; ?>');
                if (typeof name3Col !== 'undefined' && name3Col != null && name3Col.formatter) {
                    cc.push(name3Col.formatter(rowData.<?php echo $name3; ?>, rowData, rowIndex));
                } else {
                    cc.push(rowData.<?php echo $name3; ?>);
                }
            cc.push('</span>'); 

            <?php
            }
            ?>
            cc.push('</div>'); 
            cc.push('<div class="media-right">');
                <?php
                if (isset($typeRow['fields']['name4'])) {
                    $name4 = strtolower($typeRow['fields']['name4']); ?>
                    var name4Col = $(target).datagrid('getColumnOption', '<?php echo $name4; ?>');
                    if (typeof name4Col !== 'undefined' && name4Col != null && name4Col.formatter) {
                        cc.push(name4Col.formatter(rowData.<?php echo $name4; ?>, rowData, rowIndex));
                    } else {
                        cc.push(rowData.<?php echo $name4; ?>);
                    }
                    <?php
                }
            ?>
            cc.push('</div>');         
        return cc.join('');
    }
});
<?php
}
?>