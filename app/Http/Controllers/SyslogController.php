<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SyslogController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Log list
     *
     * @return response
     */
    public function index(Request $request)
    {
        $syslogs = app('db')->table('SystemEvents');
        if($request->filled('host')) {
        	$syslogs = $syslogs->where('FromHost', $request->host);
        }
        if($request->filled('tag')) {
            $tag = $request->tag;
            if(starts_with($tag,'!')) {
                $tag = str_replace_first('!','',$tag);
                $syslogs = $syslogs->where('SysLogTag','<>', $tag);
            } else {
                $operator = "=";
                $tag = $request->tag;
                $tag = str_replace('%', '', $tag);
                $operator = starts_with($tag,'*') || ends_with($tag,'*') ? 'like' : '=';
                if(starts_with($tag,'*')) $tag = str_replace_first('*','%', $tag);
                if(ends_with($tag,'*')) $tag = str_replace_last('*','%', $tag);
                $syslogs = $syslogs->where('SysLogTag',$operator, $tag);
            }
        }
        if($request->filled('priority')){
            if(preg_match('/^0?,1?,2?,3?,4?,5?,6?,7?$/', $request->priority)){
                $priorities = explode(',', $request->priority);
                $priorities = array_where($priorities, function($v,$k) { return preg_match('/^[0-7]{1}$/', $v); });
                if(count($priorities) > 0) $syslogs = $syslogs->whereIn('Priority', $priorities);
            }
        }
        if($request->filled('message')) {
            $operator = "=";
            $message = $request->message;
            $message = str_replace('%', '', $message);
            $operator = starts_with($message,'*') || ends_with($message,'*') ? 'like' : '=';
            if(starts_with($message,'*')) $message = str_replace_first('*','%', $message);
            if(ends_with($message,'*')) $message = str_replace_last('*','%', $message);
        	$syslogs = $syslogs->where('Message',$operator, $message);
        }
        $syslogs = $syslogs->orderBy('DeviceReportedTime', 'desc')->orderBy('ID', 'desc')->paginate(50);
        return response()->json(['logs' => $syslogs], 200, ['Access-Control-Allow-Origin' => '*']);
    }
}
