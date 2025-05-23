<?php 

class Response{
    private $data;
    private $status;
    private $error = "Generic Error";

    public function __construct()
    {
        $this->status=0;
        $this->data = array("error"=>"Generic Error");
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function set($status, $data)
    {
        $this->setData($data);
        $this->setStatus($status);
    }

    public function encode()
    {
        if($this->status==0)
        {
            $this->data = array("error"=>$this->error);
            $response = array("status"=>$this->status,"data"=>$this->data);
        }
        else{
            $response = array("status"=>$this->status, "data"=>$this->data);
        }
        return json_encode($response,JSON_PRETTY_PRINT);
    }
}

?>