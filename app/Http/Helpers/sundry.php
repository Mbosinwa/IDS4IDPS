<?php

/**
 * @param string $action    Controller@method
 * @param string $namespace No backslash at the beginning or end
 * @param array $args       key->value pairs or just simple array
 * @deprecated Call the action the PHP way
 * @return mixed
 */
function run_action($action, $namespace = null, $args = [])
{
    $request = request();
    $action = 'App\Http\Controllers' . ($namespace ? '\\' . $namespace . '\\' . $action : '\\' . $action);

    list($class) = explode('@', $action);
    $method = explode('@', $action)[1];

    $reflector = new ReflectionMethod($class, $method);

    $pass = [];
    $i = 1;
    foreach ($reflector->getParameters() as $param) {

        /* @var $param ReflectionParameter */
        if (isset($args[$param->getName()])) {
            $pass[] = $args[$param->getName()];
        } elseif (is_object($paramClass = $param->getClass()) and $paramClass->name == get_class($request)) {
            $pass[] = $request;
        } elseif ($param->isDefaultValueAvailable()) {
            $pass[] = $param->getDefaultValue();
        } else {
            throw new InvalidArgumentException("Missing value for argument {$i} of {$class}::{$method}");
        }
        $i++;
    }

    return $reflector->invokeArgs(new $class, $pass);
}

/**
 * @return string
 */
function redirectOnAuthSuccess()
{
    return redirect()->intended()->getTargetUrl();
}

/**
 * @param $data
 *
 * @return \Illuminate\Http\JsonResponse
 */
function to_json($data)
{
    if (is_json($data))
        return $data;

    return response()->json($data);
}

/**
 * Gets Alexa traffic rank for the given url
 *
 * @param string $url
 *
 * @return array
 */
function alexa_rank($url = null)
{
    $global_rank = 0;
    $country_rank = 0;
    $website = '';
    $country = '';

    if (empty($url)) {
        $url = url('/');
    }

    $xml = simplexml_load_file("http://data.alexa.com/data?cli=10&url={$url}");
    if (isset($xml->SD)) {
        if (isset($xml->SD->POPULARITY)) {
            $global_rank = $xml->SD->POPULARITY->attributes()->TEXT;
            $website = rtrim($xml->SD->POPULARITY->attributes()->URL, '/');

            if (isset($xml->SD->COUNTRY)) {
                $country = $xml->SD->COUNTRY->attributes()->NAME;
                $country_rank = $xml->SD->COUNTRY->attributes()->RANK;
            }
        }
    }

    return [
        'grank' => (int) $global_rank,
        'crank' => (int) $country_rank,
        'website' => (string) $website,
        'country' => (string) $country,
    ];
}

/**
 * @param string $view
 * @param array $data
 * @param boolean $cache
 *
 * @return mixed
 */
function iResponse($view, $data = [], $cache = false)
{
    if (request()->wantsJson()) {
        $response = response()->json($data);
    } else {
        $response = response()->view($view, $data);
    }
    if ($cache) {
        return $response->header('cache-control', 'public');
    } else {
        return $response;
    }
}

/**
 * More intelligent interface to system calls
 * @link http://php.net/manual/en/function.system.php
 *
 * @param $cmd
 * @param string $input
 *
 * @return array
 */
function iExec($cmd, $input = '')
{
    $process = proc_open($cmd, [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
    fwrite($pipes[0], $input);
    fclose($pipes[0]);
    $stdout = stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[2]);
    $rtn = proc_close($process);

    return [
        'stdout' => $stdout,
        'stderr' => $stderr,
        'return' => $rtn
    ];
}

/**
 * @return bool
 */
function usingSocialite()
{
    return request()->session()->has('SocialitePack');
}

/**
 * Get model class represented by this string
 * @param string $type string representation of a model
 * @return type class | null
 */
function getModelClass($type)
{
    $key = trim(strtolower($type));
    if (array_key_exists($key, MODEL_MAP)) {
        return MODEL_MAP[$key];
    }
    return null;
}

/**
 * Get string representation of class
 * @param class $class
 * @return string | null
 */
function getModelString($class)
{
    if (is_object($class)) {
        $class = get_class($class);
    }
    $model_map = array_flip(MODEL_MAP);
    if (array_key_exists($class, $model_map)) {
        return $model_map[$class];
    }
    return null;
}

/**
 * Title case of a string.
 * original Title Case script © John Gruber <daringfireball.net>
 * Javascript port © David Gouch <individed.com>
 * This PHP port by Kroc Camen <camendesign.com>
 * @param string $title
 * @return string
 */
function toTitleCase($title)
{
    //remove HTML, storing it for later
    //       HTML elements to ignore    | tags  | entities
    $regx = '/<(code|var)[^>]*>.*?<\/\1>|<[^>]+>|&\S+;/';
    preg_match_all($regx, $title, $html, PREG_OFFSET_CAPTURE);
    $title = preg_replace($regx, '', strtolower($title));

    //find each word (including punctuation attached)
    preg_match_all('/[\w\p{L}&`\'‘’"“\.@:\/\{\(\[<>_]+-? */u', $title, $m1, PREG_OFFSET_CAPTURE);
    foreach ($m1[0] as &$m2) {
        //shorthand these- "match" and "index"
        list ($m, $i) = $m2;

        //correct offsets for multi-byte characters (`PREG_OFFSET_CAPTURE` returns *byte*-offset)
        //we fix this by recounting the text before the offset using multi-byte aware `strlen`
        $i = mb_strlen(substr($title, 0, $i), 'UTF-8');

        //find words that should always be lowercase…
        //(never on the first word, and never if preceded by a colon)
        $m = $i > 0 && mb_substr($title, max(0, $i - 2), 1, 'UTF-8') !== ':' &&
                !preg_match('/[\x{2014}\x{2013}] ?/u', mb_substr($title, max(0, $i - 2), 2, 'UTF-8')) &&
                preg_match('/^(a(nd?|s|t)?|b(ut|y)|en|for|i[fn]|o[fnr]|t(he|o)|vs?\.?|via)[ \-]/i', $m) ? //…and convert them to lowercase
                mb_strtolower($m, 'UTF-8')

                //else:	brackets and other wrappers
                : ( preg_match('/[\'"_{(\[‘“]/u', mb_substr($title, max(0, $i - 1), 3, 'UTF-8')) ? //convert first letter within wrapper to uppercase
                mb_substr($m, 0, 1, 'UTF-8') .
                mb_strtoupper(mb_substr($m, 1, 1, 'UTF-8'), 'UTF-8') .
                mb_substr($m, 2, mb_strlen($m, 'UTF-8') - 2, 'UTF-8')

                //else:	do not uppercase these cases
                : ( preg_match('/[\])}]/', mb_substr($title, max(0, $i - 1), 3, 'UTF-8')) ||
                preg_match('/[A-Z]+|&|\w+[._]\w+/u', mb_substr($m, 1, mb_strlen($m, 'UTF-8') - 1, 'UTF-8')) ? $m
                //if all else fails, then no more fringe-cases; uppercase the word
                : mb_strtoupper(mb_substr($m, 0, 1, 'UTF-8'), 'UTF-8') .
                mb_substr($m, 1, mb_strlen($m, 'UTF-8'), 'UTF-8')
                ));

        //resplice the title with the change (`substr_replace` is not multi-byte aware)
        $title = mb_substr($title, 0, $i, 'UTF-8') . $m .
                mb_substr($title, $i + mb_strlen($m, 'UTF-8'), mb_strlen($title, 'UTF-8'), 'UTF-8')
        ;
    }

    //restore the HTML
    foreach ($html[0] as &$tag)
        $title = substr_replace($title, $tag[0], $tag[1], 0);
    return $title;
}

/**
 * Construct message in default notification format
 * @param string $title Notification title
 * @param array $options (Optional)<br/>
 * <b>image</b>: Primary notification image. An array with keys model and id,
 * defining the object string and id respectively <br/>
 * <b>text</b>: Secondary text or subtitle.<br/>
 * <b>circle</b>: True if primary image should be circle else false.<br/>
 * <b>secondaryImage</b>: Secondary notification image. An array with keys model and id,
 * defining the object string and id respectively<br/>
 * <b>secondaryCircle</b>: True if secondary image should be circle else false.
 *
 * @return string
 */
function defaultNotificatonTemplate($title, array $options = [])
{
    $options['title'] = $title;
    return $options;
}

function getAnchor($model, $title = null, $highlight = true)
{
    if (empty($title)) {
        try {
            $title = $model->name;
        } catch (Exception $e) {
            
        }
    }

    $anchor = '<a href="' . $model->url . '"';
    if ($highlight) {
        $anchor .= ' class="font-bold"';
    }
    $anchor .= '>' . $title . '</a>';
    return $anchor;
}
