<?php

interface HttpClientInterface
{
    public function post($command, $options = []);
    public function get($command, $options = []);
    public function delete($command, $options = []);
}
