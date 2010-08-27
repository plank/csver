<?php
class CsverController extends CsverAppController {

    var $debug = false;


    /**
     * CSV Index.
     *
     * @param string $type Will look to see if method admin_$type exists, in which case, execute that, else look for a model by that name, and do generic output.
     * 
     * @return void
     **/
    function admin_index($type = false) {
        if(false == $this->debug) {
            Configure::write('debug', 0);
        }

        if(!$type) {
            echo 'Model name missing';
            exit();
        }

        $this->type = $type;

        App::import('Vendor', 'Csver.CsvWriter', array('file'=>'php-csv/csv.php'));

        //Can define some custom types, ie: to load up certain data or whatnot
        //@todo this is example, but implement when needed.
        if(method_exists($this, 'admin_'.$type)) {
            $filename = call_user_method('admin_'.$type, $this);
        }
        else {
            //We don't use Controller::loadModel() due to bug where if successful, doesn't return true. 
            //If this fails, will just get a 404 error, so we don't botherany extra checking. 
            $model = ClassRegistry::init($type);
            
            $data = $model->csv();
            
            $filename = tempnam(TMP, '');
            $file = new CsvWriter($filename);
            //Add header row
            $file->addLine($data['header']);
            foreach ($data['results'] as $line) {
                $file->addLine($line[$type]);
            }
        }


        $this->_output($filename);
    }
    
    
    /**
     * Send output headers.
     *
     * @return void
     **/
    function _headers() {

        $downloadfilename = $this->type.'.'.date('Y_M-d-G_i').'.csv';

        if(false == $this->debug) {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment;filename="'.$downloadfilename.'"');
            header('Cache-Control: max-age=0');
        }
    }

    /**
     * Output the file
     *
     * @return void
     **/
    function _output($filename) {
        
        $this->layout = false;
        $this->autoRender = false;

        if(file_exists($filename)) {
            $this->_headers();
            readfile($filename);
            unlink($filename);
        }
    }
    
    
    
}
