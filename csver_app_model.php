<?php
class CsverAppModel extends AppModel {
    var $useTable = false;
    
    
    /**
     * This is the default find.
     * If you want something other than the default, you can create this method in your model
     * and just return the result and header
     *
     * @return array array('header', 'results');
     **/
    function csv() {
    
        $results = array(
            'header' => array(),
            'results' => array()
        );
        
        //Gather the fields: Everything except ID
        $fields = array_keys($this->_schema);
        $idIndex = array_search('id', $fields, true);
        if(false !== $idIndex) {
            unset($fields[$idIndex]);
        }

        $data = $this->find('all', array('fields'=>$fields));


        $results['header'] = $fields;
        $results['results'] = $data;
        
        return $results;
        
    }
    
}
?>