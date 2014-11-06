<?php 
namespace RAAS\CMS;

$_RAASForm_Control = function(\RAAS\Field $Field, $confirm = true) use (&$_RAASForm_Attrs, &$_RAASForm_Options, &$_RAASForm_Checkbox) {
    
    if ($Field->multiple) { 
        ?>
        <div data-role="raas-repo-block">
          <div data-role="raas-repo-container">
            <?php foreach ((array)$Field->Form->DATA[$Field->name] as $key => $val) { 
                $Material = new Material((int)$val);
                $attrs = array(
                  'datatype' => 'material', 
                  'type' => 'hidden', 
                  'data-field-id' => (int)$Field->Form->Item->fields[$Field->name]->id,
                  'data-material-id' => $Material->id, 
                  'data-material-pid' => $Material->parents[0]->id, 
                  'data-material-name' => $Material->name
                );
                ?>
                <div data-role="raas-repo-element"><input<?php echo $_RAASForm_Attrs($Field, array_merge($attrs, array('value' => $val)))?> /></div>
            <?php } ?>
          </div>
          <div data-role="raas-repo">
            <?php
            $attrs = array(
              'datatype' => 'material', 
              'type' => 'hidden', 
              'data-field-id' => (int)$Field->Form->Item->fields[$Field->name]->id,
              'data-material-id' => '', 
              'data-material-pid' => '', 
              'data-material-name' => ''
            );
            ?>
            <input<?php echo $_RAASForm_Attrs($Field, array_merge($attrs, array('disabled' => 'disabled')))?> />
          </div>
        </div>
        <?php 
    } else { 
        $Material = new Material((int)$Field->Form->DATA[$Field->name]);
        $attrs = array(
          'datatype' => 'material', 
          'type' => 'hidden', 
          'data-field-id' => (int)$Field->Form->Item->fields[$Field->name]->id,
          'data-material-id' => $Material->id, 
          'data-material-pid' => $Material->parents[0]->id, 
          'data-material-name' => $Material->name
        );
        ?>
        <input<?php echo $_RAASForm_Attrs($Field, array_merge($attrs, array('value' => $Field->Form->DATA[$Field->name])))?> />
        <?php
    }
};