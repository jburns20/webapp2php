<?php

class NotFoundHandler extends Handler {
    
    public function get() {
        $this->response->error(404);
        $this->write("<h1>Error 404: File Not Found.</h1>");
    }

    public function post() {
        $this->response->error(404);
        $this->write("<h1>Error 404: File Not Found.</h1>");
    }
    
}