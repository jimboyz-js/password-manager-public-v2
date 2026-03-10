<?php
/**
 * @author jimBoYz Ni ChOy!!!
 */
function table($tr = []) {
?>
<div class="table-responsive">
    <table id="table" class="table table-striped table-bordered table-hover align-middle">
        <thead class="table-dark">
            <tr class="text-center">
                <th><?=$tr["id"]?></th>
                <th><?=$tr[1]?></th>
                <th><?=$tr[2]?></th>
                <th><?=$tr[3]?></th>
                <th><?=$tr[4]?></th>
                <th><?=$tr[5]?></th>
                <th><?=$tr[6]?></th>
                <th><?=$tr[7]?></th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
<?php
}
?>