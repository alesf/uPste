<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Upload;
use App\Models\User;
use Auth;
use Illuminate\Mail\Message;
use Mail;

class AccountController extends Controller
{
    public function getIndex()
    {
        // Check if the user has been registered for 7 days or less
        $now = time();
        $registeredDate = strtotime(Auth::user()->created_at);
        $dateDiff = abs($now - $registeredDate);
        $daysRegistered = 7 - round($dateDiff / (60 * 60 * 24));
        $new = ($daysRegistered > 0 && $daysRegistered <= 7);

        return view('account.index', compact('new', 'daysRegistered'));
    }

    public function getResources()
    {
        return view('account.resources');
    }

    public function getFaq()
    {
        return view('account.faq');
    }

    public function getBashScript()
    {
        return response()->view('account.resources.bash')->header('Content-Type', 'text/plain');
    }

    public function getUploads()
    {
        $uploads = Upload::whereUserId(Auth::user()->id)->orderBy('updated_at', 'desc')->paginate(15);

        return view('account.uploads', compact('uploads'));
    }

    public function postUploadsDelete(Upload $upload)
    {
        if (Auth::user()->id != $upload->user_id) {
            flash()->error('That file is not yours, you cannot delete it!');

            return redirect()->back();
        }

        $upload->forceDelete();
        flash()->success($upload->original_name . ' has been deleted.');

        return redirect()->back();
    }

    public function postResetKey()
    {
        do {
            $newKey = str_random(64);
        } while (User::whereApikey($newKey)->first());

        $user = Auth::user();
        $user->fill(['apikey' => $newKey])->save();
        flash()->success('Your API key was reset. New API key: ' . $user->apikey)->important();

        Mail::queue(['text' => 'emails.user.api_key_reset'], $user->toArray(), function (Message $message) use ($user) {
            $message->subject(sprintf("[%s] API Key Reset", env('DOMAIN')));
            $message->to($user->email);
        });

        return redirect()->route('account');
    }
}
