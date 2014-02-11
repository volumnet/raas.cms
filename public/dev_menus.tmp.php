<?php if ($Set) { ?> 
    <form action="" method="post">
      <table class="table table-striped">
        <thead>
          <tr>
            <?php foreach (array('name' => NAME, 'url' => CMS\URL, 'priority' => CMS\PRIOR) as $key => $val) { ?>
                <th><?php echo $val?></th>
            <?php } ?>
            <th style="width: 120px;"><?php echo MANAGEMENT?></th>
          </tr>
        </thead>
        <tbody>
          <?php for($i = 0, $j = 0; $i < count($Set); $i++) { $row = $Set[$i]; ?>
              <tr>
                <?php if ($row->realized || !$Item->id) { $j++?>
                    <td>
                      <a href="?p=<?php echo $VIEW->packageName?>&sub=<?php echo $VIEW->sub?>&action=menus&id=<?php echo (int)$row->id?>" class="<?php echo (!$row->vis ? ' muted' : '') . ($row->pvis ? '' : ' cms-inpvis')?>">
                        <?php echo htmlspecialchars($row->name)?>
                      </a>
                    </td>
                    <td class="<?php echo (!$row->vis ? ' muted' : '') . ($row->pvis ? '' : ' cms-inpvis')?>"><?php echo htmlspecialchars($row->url)?></td>
                    <td><input type="text" class="span1" maxlength="3" name="reorder[<?php echo (int)$row->id?>]" value="<?php echo (int)$row->priority?>" /></td>
                    <td><?php echo rowContextMenu(\RAAS\CMS\ViewSub_Dev::i()->getMenuContextMenu($row, $i, count($Set)))?></td>
                <?php } else { ?>
                    <td><?php echo htmlspecialchars($row->name)?></td>
                    <td><?php echo htmlspecialchars($row->url)?></td>
                    <td><?php echo htmlspecialchars($row->priority)?></td>
                    <td>&nbsp;</td>
                <?php } ?>
              </tr>
          <?php } ?>
          <?php if ($j) { ?>
              <tr><td>&nbsp;</td><td>&nbsp;</td><td><input type="submit" class="btn" value="<?php echo DO_UPDATE?>" /></td><td>&nbsp;</td></tr>
          <?php } ?>
        </tbody>
      </table>
    </form>
<?php } elseif ($Item->id) { ?>
    <?php echo CMS\NO_NOTES_FOUND?>
<?php } ?>