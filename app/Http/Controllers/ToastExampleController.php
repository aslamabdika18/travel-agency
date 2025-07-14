<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ToastExampleController extends Controller
{
    /**
     * Toast notification usage examples
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.toast-example');
    }

    /**
     * Success example
     *
     * @return \Illuminate\Http\Response
     */
    public function success()
    {
        return redirect()->route('toast.example')
            ->with('toast_success', 'Operation completed successfully!');
    }

    /**
     * Error example
     *
     * @return \Illuminate\Http\Response
     */
    public function error()
    {
        return redirect()->route('toast.example')
            ->with('toast_error', 'An error occurred while processing the request!');
    }

    /**
     * Warning example
     *
     * @return \Illuminate\Http\Response
     */
    public function warning()
    {
        return redirect()->route('toast.example')
            ->with('toast_warning', 'Warning! This is a warning message.');
    }

    /**
     * Info example
     *
     * @return \Illuminate\Http\Response
     */
    public function info()
    {
        return redirect()->route('toast.example')
            ->with('toast_info', 'Information: This is an informational message.');
    }

    /**
     * JavaScript example
     *
     * @return \Illuminate\Http\Response
     */
    public function javascript()
    {
        return view('pages.toast-javascript-example');
    }
}