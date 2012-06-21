<?php
class datagrid {
//put your code here
    public $dataset;
    public $name;
    public $fields;
    public $cssClass;
    public $altClass;
    public $readOnly;
    public $customColumns;
    public $itemID;
    public $action;
    public $selectColumn;

    function  __construct($name, $dataset) {
        $this->name = $name;
        $this->dataset = $dataset;
    }

    function databind() {
        if(isset($this->dataset)) {
            echo "\t".'<table name="'.$this->name.'" id="'.$this->name.'" class="'.$this->cssClass.'">'."\n\t\t<thead>\n";

            //get fields
            echo "\n\t\t\t<tr>\n";

            //check if not readonly to include an extra column for selection
            if (isset($this->readOnly) && !$this->readOnly && $this->selectColumn==true) {
                echo "\t\t\t\t<th>".'<input type="checkbox" class=""/>' ."</th>" ."\n";
            }

            if (!isset($this->fields)) {
            //display all fields
                $field_offset=0;
                while($field_offset < mysql_numfields($this->dataset)) {
                    $fieldname=mysql_fetch_field($this->dataset, $field_offset);
                    echo "\t\t\t\t<th>" . $fieldname->name . "</th>" ."\n";
                    $field_offset++;
                }

            }
            else {
            //display selected fields
            //check if assoc array
                if (is_array($this->fields) && array_diff_key($this->fields,array_keys(array_keys($this->fields)))) {
                //display field names as specified by user
                    foreach($this->fields as $field => $value) {
                        echo "\t\t\t\t<th>" . $field . "</th>" ."\n";
                    }
                }
            }

            //check if not readonly to include an extra column for selection
            if (isset($this->readOnly) && !$this->readOnly && isset($this->customColumns)) {
                 foreach($this->customColumns as $option)
                    echo "\t\t\t\t<th>&nbsp;</th>" ."\n";
            }
            echo "\t\t\t</tr>\n\t\t</thead>\n\t\t<tbody>\n";

            //get records
            $count = 0;
            while($record = $this->dataset->fetch()) {
            //set alt color
                if (isset($this->altClass)) {
                    if ($count % 2)
                        echo "\t\t\t". '<tr class="'.$this->altClass.'">' ."\n";
                }
                else
                    echo "\t\t\t".'<tr>' ."\n";

                //check if not readonly to include an extra column for selection
                if ((isset($this->readOnly) && !$this->readOnly && $this->selectColumn==true)) {
                    echo "\t\t\t\t<td>".'<input type="checkbox" name="'.$this->itemID.'[]" value="'.$record[$this->itemID].'" class=""/>' ."</td>" ."\n";
                }

                if(!isset($this->fields)) {
                //display all fields
                    foreach($record as $field => $value) {
                        echo "\t\t\t\t".'<td>' . $value . '</td>' ."\n";
                    }
                }
                else {
                //display only selected columns
                    foreach($record as $field => $value) {
                        if (in_array($field, $this->fields))
                            echo "\t\t\t\t".'<td>' . $value . '</td>' ."\n";
                    }
                }

                if (isset($this->readOnly) && !$this->readOnly) {

                    if (isset($this->customColumns) && is_array($this->customColumns)) {
                        foreach($this->customColumns as $option) {
                           
                            if (isset($this->action))
                                echo "\t\t\t\t<td>" .'<a href="'.$this->action.'?id='.$record[$this->itemID] .'&op='.$option.'">'.$option.'</a>'. "</td>" ."\n";
                            else
                                echo "\t\t\t\t<td>" .'<a href="?id='.$record[$this->itemID] .'&op='.$option.'">'.$option.'</a>'. "</td>" ."\n";
                        }
                    }
                }
                echo "\t\t\t</tr>\n\n";
                $count++;
            }
            echo "\t\t</tbody>\n\t</table>\n";
        }
        else
            echo "No records found!";
    }
}
?>