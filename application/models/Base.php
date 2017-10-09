<?php

class Application_Model_Base extends Zend_Db_Table_Abstract
{
	public function add(Array $params){
		try{
            return $this->insert($params);
        }catch(Exception $e){
            return $e;
        }
	}

	public function remove($campo, $id){
        try{
            if(is_array($id)){
                foreach ($id as $value) {
                    $this->delete("$campo = ". (int)$value);
                }
            }else{
                $params = array('isDeleted' => 1);
                $this->edit('id_admin', $id, $params);
            }
            return true;
        }catch(Exception $e){
            return $e;
        }
    }

    public function removeWithConditions($conditions){
        try {
            $condition_txt = "";
            $count = 0;
            foreach ($conditions as $condition=>$value) {
                if ($count > 0) {
                    $condition_txt .= " AND ";
                }
                $condition_txt = $condition_txt.''.$condition.' = '.$value;
                $count++;
            }
            
            $this->delete("{$condition_txt}");
            return true;
        } catch(Exception $e){
            print_log($e);
        }
    }

    public function edit($campo,$id,$params){
        try{
            return $this->update($params, array("$campo = '$id'"));
        }catch(Exception $e){
            return $e;
        }
    }

    public function editWithConditions($params, $where){
        try{
            return $this->update($params, $where);
        }catch(Exception $e){
            return $e;
        }
    }
}
