<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;


class MailChimpController extends Controller

{

    /**

     * Write code on Method

     *

     * @return response()

     */

    public function index(Request $request)

    {

        $listId = env('MAILCHIMP_LIST_ID');


        $mailchimp = new \Mailchimp(env('MAILCHIMP_KEY'));


        $campaign = $mailchimp->campaigns->create('regular', [

            'list_id' => $listId,

            'subject' => 'Example Mail',

            'from_email' => 'pallavi@meritest.in',

            'from_name' => 'Pallavi',

            'to_name' => 'Pallavi Subscribers'


        ], [

            // 'html' => $request->input('content'),

            // 'text' => strip_tags($request->input('content'))

            'html' => "<html>testing</html>",

            'text' => "testing"

        ]);


        //Send campaign

        $mailchimp->campaigns->send($campaign['id']);


        dd('Campaign send successfully.');

    }

}
