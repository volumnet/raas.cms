<?php if ($Set) { ?> 
    <table class="table table-striped">
      <thead>
        <tr>
          <?php foreach (array('name' => NAME, 'urn' => CMS\DOMAIN) as $key => $val) { ?>
              <th>
                <a href="<?php echo \SOME\HTTP::queryString('sort=' . $key . '&order=' . (($sort == $key || !$sort) && $order == 'asc' ? 'desc' : 'asc'))?>">
                  <?php echo $val . (($sort == $key || (!$sort && $key == 'urn')) ? (' ' . ($order == 'asc' ? '&#9650;' : '&#9660;')) : '')?>
                </a>
              </th>
          <?php } ?>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php for($i = 0, $row = $Set[$i]; $i < count($Set); $i++, $row = $Set[$i]) { ?>
            <tr>
              <td>
                <a href="?p=<?php echo $VIEW->packageName?>&sub=<?php echo $VIEW->sub?>&id=<?php echo (int)$row->id?>"<?php echo !$row->vis ? ' class="muted"' : ''?>>
                  <?php echo htmlspecialchars($row->name)?>
                </a>
              </td>
              <td>
                <a href="http://<?php echo htmlspecialchars(str_replace('http://', '', array_shift(explode(' ', $row->urn))))?>"<?php echo !$row->vis ? ' class="muted"' : ''?>>
                  <?php echo htmlspecialchars(str_replace('http://', '', array_shift(explode(' ', $row->urn))))?>
                </a>
              </td>
              <td><?php echo rowContextMenu(\RAAS\CMS\ViewSub_Main->getPageContextMenu($row))?></td>
            </tr>
        <?php } ?>
      </tbody>
    </table>
<?php } else { ?>
    <?php echo CMS\NO_SITES_FOUND?>
<?php } ?>