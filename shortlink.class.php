<?php

class shortLink {
    private $dbo;
    private $baseurl = 'http://myhost.somewere';


    function __construct($dbo) {
        $this->dbo = $dbo;
    }

    private function check_plain($str) {
        return $this->dbo->quote($str);
    }

    private function  create_url()  {
        $arr = array('a','b','c','d','e','f',
                     'g','h','i','j','k','l',
                     'm','n','o','p','r','s',
                     't','u','v','w','x','y',
                     'z','A','B','C','D','E',
                     'G','H','I','J','K','L',
                     'M','N','O','P','R','S',
                     'T','U','V','W','X','Y',
                     'Z','F','1','2','3','4',
                     '5','6','7','8','9','0');
        $url = "";
        for($i = 0; $i < 6; $i++)
        {
          $random = rand(0, count($arr) - 1);
          $url .= $arr[$random];
        }
        $exists = $this->dbo->query('select count(*) from short_links where short = "'.$url.'"')->fetchColumn();
        if ($exists)
            return $this->create_url();
        return $url;
    }

    function shorten($link) {
        $exists = $this->dbo->query('select * from short_links where link ='.$this->check_plain($link))->fetchObject();
        if (is_object($exists)) {
            return $this->baseurl.'/'.$exists->short;
        }
        $short = $this->create_url();
        $this->dbo->exec('insert into short_links (short, link) values ("'.$short.'", '.$this->check_plain($link).');');
        return $this->baseurl.'/'.$short;
    }

    function getOrig($short) {
        $exists = $this->dbo->query('select * from short_links where short ='.$this->check_plain($short))->fetchObject();
        return  is_object($exists) ? $exists->link : '';
    }

    function initiateDB () {
        //drop if exist
        $this->dbo->exec('drop table if exists `short_links`');
        //create table
        $create = "CREATE TABLE `short_links` (
            `id` INT NOT NULL AUTO_INCREMENT ,
            `short` VARCHAR(64) NOT NULL ,
            `link` VARCHAR(255) NOT NULL ,
            PRIMARY KEY (`id`),
            INDEX (`short`),
            INDEX (`link`));";

        $this->dbo->exec($create);
    }

    function dbValid($version = '1.0') {
        //table exists and valid
        $sql = 'show tables like "short_links"';
        $valid = false;
        foreach ($this->dbo->query($sql) as $row) {
            $valid = true;
        }
        return $valid;
    }

}
