<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost:8000'),

    'asset_url' => env('ASSET_URL'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

    'faker_locale' => 'en_US',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => 'file',
        // 'store'  => 'redis',
    ],

    /**
     * ------------------------------------------------------------------------
     * API Version
     * ------------------------------------------------------------------------
     * Here we need to specify what the api version  is
     */
    'apiversion' => env('API_VERSIOn', 'v1'),

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => ServiceProvider::defaultProviders()->merge([
        /*
         * Package Service Providers...
         */
        Spatie\Permission\PermissionServiceProvider::class,

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
    ])->toArray(),

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => Facade::defaultAliases()->merge([
        // 'Example' => App\Facades\Example::class,
    ])->toArray(),


    'razorpay_api_key' => env('RAZORPAY_API_KEY', null),

    'account_name' => env('ACCOUNT_NAME', null),

    'currency' => env('CURRENCY', null),

    'secret_key' => env('SECRET_KEY', null),

    'master_terms_conditions' => 'Annexure - General Terms & Conditions :
    A. General Terms :
    1) Contractor is fully responsible for all associated activities to complete the Work.
    2) No construction activities will be carried out without responsible contractor representative on site.
    3) Sample - Single sample to be made and get it approved from Owner’s representative/PMC/site incharge prior to actual start of work.
    4) Scaffolding - All the scaffolding required for completion of mentioned work is in Contractor’s scope.
    5) Housekeeping - Contractor shall clean & clear the site after completion of work and maintain proper housekeeping during    execution of the work at site.
    6) DLP (Defect liability period) – Defect Liability Period shall be 12 Months from the date of final bill certification. Retention hold amount shall be released after successfully completion of DLP. The Contractor will correct and replace any defects occurring within a period of one year from the date of completion. Any minor modifications and corrective works required by the customer of the builder within this period will be done by the Contractor, free of cost.
    7) The Builder shall not be liable for any claim for loss or profits or any other consequential or direct / indirect damages that may be suffered by the Contractor during the execution of the work.
    8) Completion Period: Time is the essence of this order and hence the execution schedule should be strictly adhered to. Contractor should start the work immediately on site after the instructions of site in charge or the date of issue of this work order whichever is earlier.  In general the complete mentioned works for the said Tower shall be completed latest by ___________. The schedule as attached to be strictly followed.
    9)  2 (two) copies of Contractor’s invoices along with supporting  documents duly certified by Site Engineer, clearly indicating the Order number shall be addressed to the Owner and submitted to the Owner Site office.
    10) All contractual prices, agreed and defined as compensation including the taxes plus profit and covering all contractual obligations and risks as indicated in the Order are understood to be fixed and firm and not subject to escalation up to fulfillment of Contractual Obligations of both the Parties and shall not be subject to any adjustment due to any reason such as but not limited to any statutory variations.
    11) Contractor shall deploy his representative - (Engineer / Supervisor) on site during work execution & adequate supervisors, safety persons should be on site as per site requirement. (Contractor shall submit the staff appointment details as per the work schedule and get it approved from Owner’s PM). While on duty, Contractor’s personnel shall report to Owner’s representative at Site and shall dutifully carry out the task assigned to them and follow the instructions.
    12) Mathadi/Transport/Lifting & Shifting : Contractor shall be responsible to pay Mathadi charges if any for his material. All local issues, political issues related to work shall be handled by Contractor. The Contractor rates shall include all the arrangement for machinery or manpower for material transportation, lifting & Shifting.
    13) If any deviation in design & drawing, then same should be intimated by Contractor well in advance & before execution of additional quantity/extra item.
    14) Liquidity damage:  For the subject, Contractor shall pay to Owner, as fixed agreed and liquidated damages for each week of delay, the sum 0.5% of contract price (excluding taxes), per week to the maximum limit of 5% of the contract price (excluding taxes).
    15) As this is the Lum-sum based contracts so, there will be no any additional qty variation and No extra charges will be paid in case there is any increase in the quantities if found at a later date. However, deduction will be made in reimbursement/payment if the final checked quantities will be less than those mentioned in the BOQ.
    16) The Builder, if applicable, will deduct any Tax, at source as per prevailing provisions of the law.
    17) Retention 5% of the total cost will be done and returned after a period of 1 year from the date of handing over.
    18) Force de majure - Owner shall not be held responsible for any loss in work due to environmental factors; though the clause of ‘Force de majure’ shall be applicable shall be in event of any natural calamity. In case of unexpected weather conditions it shall be Contractors responsibility to make alternative or additional arrangements for the Construction works, for which he shall not be entitled to any additional compensation.
    19) Store: Contractor has to make his own arrangement for temporary office & store at site. Space for store shall be given to Contractor if available, if space is not available, Contractor will have to make his own arrangements. After completion of work, Contractor shall clear the space with removing all temporary structures made at site by Contractor as per requirement of Owner. Contractor can be asked to shift the location of office, store, to carry out the civil / finishing work at that location.The Owner shall not be held responsible for any losses in terms of theft of otherwise.
    20) Water & Electricity for works shall be provided by the Owner at one point only. Further distribution is like Wiring, bulb, halogen for the lighting shall be arranged for by the Contractor, owner shall provide an electrical outlet at a fixed point. In case of activities after dark proper permission shall be taken from the Site In charge, after displaying proper arrangement for the same. Permission of works at night shall be totally at the discretion of the owner and the request for same may be refused.
    21) Labour Camp Space, power & Water:- Labor camp will be set up by the Contractor. Arrangement of Electricity and water supply for labor camp will be in the scope of Contractor.  No labor will be allowed to live within the premises of the project / within the buildings.
    22) Contract Termination:- At the completion of works or the work order shall be terminated in case of misbehavior, disobedience, dishonesty or negligence on the part of Contractor and/or his personnel or Contractor’s failure to execute, complete and deliver the Work within the specified / reasonable time as decided by the Builder. Upon any such termination, the Builder shall pay in accordance with the following:
    * All amounts due and not previously paid to Contractor for Work completed in accordance with the Order prior to any notice of termination, and for Work completed thereafter as specified in the notice, after deducting an amount as may be considered suitable to adjust for loss of work caused due to the Termination.
    * The owner has all rights, to recover from the Contractor, any loss that is incurred or foreseen due to the Contractor’s default. The Builder shall not be held responsible for any damage, either direct and / or indirect, to the Work Contractor consequent on his exercising his right to early terminate or suspend the Order.

    B. Quality & Safety
    1) Safety - Contractor’s personnel deputed for the Work shall comply with all rules & regulations including the safety procedures prevailing at Site/ Office and shall be medically fit to perform the Work.
    2) The contractor shall take all safety measures on site such as safety helmets, safety belts, labor insurance etc. In case of any mishap on site only the contractor will be responsible for any consequences arising for such a situation. The contractor will not have any claim for the same.
    3) No child labour will be permitted to work on site. If anyone is found, the Builder has the authority to stop the work immediately. During any governmental inspection if the same is identified, the contractor will be solely responsible for the consequences.
    4) Indemnity - Contractor shall at all time keep the Owner fully indemnified against any consequences arising out of Work Contractor’s own or on account of Contractor’s default or negligence. The Owner shall not be liable to the Contractor for any claim for loss or profits or any other consequential or indirect damages that may be suffered by the Contractor during the execution of the work.
    5) Test Certificates: Contractor shall be responsible for the MTC (Material test certificates) along with the material supplied by Contractor (Third party manufacturer). However, test which is carried out at site as per the requirement of owner/PMC quality department shall be carried out by Contractor at his own cost.  The Contractor shall furnish, at his own cost, test certificates for the various materials and equipment as called for by the Project Manager. Such test certificates should be for the consignment/lot/piece as decided by the Project Manager. The details in respect of the test certificates shall be as decided by the Project Manager for the relevant items.
    6) The Quality of work will be assessed by the Site In Charge on daily basis. Quality performance below acceptable level shall be liable to be debited from the RA Bill.
    7) Quality: Any Non-Compliance that is not attended by the Contractor, the OWNER has rights to attend & debit the costs & expenses to the account of the Contractor.
    8) Governing Law & Jurisdiction: All actions/proceedings at law or suits arising out of, or in connection with this contract or the subject matter shall be governed by the Act/ Laws applicable time to time made by state or central governments and shall be subject to the jurisdiction of Pune court.

    C. General and Statutory Obligations :
    1) All statutory obligations, permits, licenses etc. in respect of the Work shall be done by the Contractor.
    2) Insurances for the Contractor’s personnel and Equipment as applicable and in accordance with project requirements shall be arranged by the Contractor at his own cost and will submit a photocopy of all licenses and permits to the Builders office before starting work.
    3) The builder will not be liable at any time and for any type of accident/s occurred on site. Any accident, major or minor, due to electrical shock or water or any similar or any other type of accident/s with the contractor’s personnel, labour, supervisor and their family members will be the liability of the contractor. It is the duty of the contractor to take and/or keep all types of security and safety with at he required insurance at site. In the above case, the contractor and only the contractor will be responsible for any type of consequences arising for the above situation.
    4) Contractor shall submit the following documents during the execution of works as per detailed schedule to be agreed:
    * Schedule of Work with interim Milestones.
    * Labour License and details for confirmation of compliance with labour laws and provident fund Act etc., as per statutory requirements.
    * Labour Insurance details, (Workmen Compensation Policy).
    * Work Contract Tax registration details.
    * Contractor shall produce Govt. ID proof & residence proof of all his labor at site before start of work.
    ',
    'pre_define_condition' => [
        'work order no' => '',
        'm/s.' => '',
        'address' => '',
        'kind attn' => '',
        'contact no' => '',
        'e-mail' => '',
        'pan no' => '',
        'gst no' => '',
        'start Of work' => '',
        'date' => '',
        'certified and sanctioned by' => '',
        'name' => '',
        'agree and accepted name:' => '',
        'endorsed by' => '',
        'article 1' => 'PREFACE
                        (1.1- Work Order For Services on labour basis for the Carpentry works of sample flat at Destination Kharadi, Pune
                        1.2- M/s Destination Kharadi Developers LLP(hereinafter reffered to as "BUILDER")
                        1.3- M/s Amra Turnkey Interior Solution (hereinafter reffered to as "CONTRACTOR")The Contractor will be responsible for execution of the all work in accordance to this "Work Order", in particular the detailed in article 2
                        1.4- Total Compensation: On labour basis charges = 30% on material amount Rs = 10,00,000/- Total Compensation amounting to Rs. 30,00,000/- (Rs. Three Lakhs Only) GST @18% Extra.)',
        'article 2' => 'SCOPE & PERFORMANCE OF WORK CONTRACTOR
                        (2.1- in general the scope of the Contractorwill include the provision of labour for the carpentry works of
                        2.11- No construction activities will be carried out without responsible contractor representative on site
                        2.12- Contactor will be responsible for the safekeeping of hin own material. The Builder will not be held responsible dfor any losses in terms of theft of otherwise
                        2.13- Wastage will be responsibility of the Contractor. Builder will not be held responsible.
                        2.14- Completion Period
                                Time is the essence of this order and hence the execution schedule should be stricly adhered to. In general the complete Carpentry works for thesaid Sample flat will be completed latest by 10.08.2019.)',
        'article 3' => 'COMPENSATION FOR CONTRACTORS WORK',
        'article 4' => '(4.1- Contractors personnel deputed for the work shall compely with all rules & regulations including the safety procedures prevailing at sites/office and shall be medically fit to perform the work
                        4.2- The Builder shall not be liable to the contractor for any claim for loss or profits or any other consequential or indirect damages that may be suffered by the contractor during the execution of the work
                        4.3- Contractor shall at all time keep the Builder fully indemnified against any consequenes arising out of work Contractors own or on account of contractors default or negligence.
                        4.4- The Contractor shall take all safety measures on site such as safety helmets, safety belts, etc. In case any mishap on site the contractor and only the contractor will be responsible for any consequences arising for such a situation. The contractor will not have any claim for the same.
                        4.5- No child labours will be permitted to work on site. If anyone is found the builder has the authority to stop the work immediately. During any governmental inspection if the same is identified, the contractor  wil be completely responsible for the consequences.
                        4.6- General and Statutory Obligations:
                                1- All statutory obligations, permits, licenses, etc. in respect of the workshall be done by the contractor.
                                2- )'
    ]

];
