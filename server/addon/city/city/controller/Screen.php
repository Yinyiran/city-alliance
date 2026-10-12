<?php


namespace addon\city\city\controller;


class Screen extends BaseCity
{
    public function lists()
    {
        $domain = request()->domain();
        $url = $domain . '/pc?website_id='.$this->site_id;
//        return $this->fetch('screen/lists', ['url' => $url], $this->replace);
        return redirect($url);
    }
}