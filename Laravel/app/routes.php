<?php


/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Register all the admin routes.
|
*/

Route::group(array('prefix' => 'api'), function()
{
		Route::post('student/signin', array('as' => 'student.signin', 'uses' => 'AuthController@postStudentSignin'));
		Route::get('student/logout', array('as' => 'student.logout', 'uses' => 'AuthController@getStudentLogout'));		
	
		Route::any('instances/getInstanceById','Controllers\Api\PublicController@getInstanceById');
		Route::get('coursebundles/bundles','Controllers\Api\PublicController@getBundles');
		Route::get('bundles','Controllers\Api\PublicController@getAllBundles');
		Route::post('booking/processPurchase','Controllers\Api\PublicController@processPurchase');
		Route::post('booking/payWayPurchase','Controllers\Api\PublicController@payWayPurchase');
		Route::post('booking/paypalPurchase','Controllers\Api\PublicController@paypalPurchase');
		Route::post('booking/submitToPayPal','Controllers\Api\PublicController@submitToPayPal');
		Route::any('booking/cancelPayPalPurchase','Controllers\Api\PublicController@cancelPayPalPurchase');
		Route::any('booking/completePayPalPurchase','Controllers\Api\PublicController@completePayPalPurchase');
		
		Route::any('booking/cancelPayWayPurchase','Controllers\Api\PublicController@cancelPayWayPurchase');
		Route::any('booking/completePayWayPurchase','Controllers\Api\PublicController@completePayWayPurchase');
		Route::any('booking/queuePayWayServerResponse','Controllers\Api\PublicController@queuePayWayServerResponse');
		
		Route::any('booking/thankyou', array('as' => 'api.booking.thankyou', 'uses' => 'Controllers\Api\PublicController@completeBooking'));
		
		Route::any('booking/{id}','Controllers\Api\PublicController@getBookingDetails');

		Route::get('invoices/download/{id}', array('as' => 'api.invoices.download', 'uses' => 'Controllers\Api\PublicController@download'));
		Route::get('vouchers/download/{id}', array('as' => 'api.vouchers.download', 'uses' => 'Controllers\Api\PublicController@voucherDownload'));
		
		//not public
		Route::get('invoices/email/{id}/{recreate}', array('as' => 'api.invoices.email', 'uses' => 'Controllers\Api\OrdersController@emailInvoice'));

		Route::any('groupbooking/{id}','Controllers\Api\BookingsController@getGroupBookingDetails');
		Route::any('purchase/{id}','Controllers\Api\BookingsController@getPurchaseDetails');

		Route::post('attachments/upload','Controllers\Api\AttachmentsController@upload');
		
		Route::any('emails/testEmail','Controllers\Api\MessagesController@testEmail');
		Route::any('emails/testMarketingEmail','Controllers\Api\MessagesController@testMarketingEmail');
		Route::any('emails/processMarketingEmail','Controllers\Api\MessagesController@processMarketingEmail');
		Route::get('emails/unsubscribe/{id}', array('as' => 'api.emails.unsubscribe', 'uses' => 'Controllers\Api\PublicController@emailUnsubscribe'));
		Route::any('emails/viewMarketingEmail/{id}/{customer_id}', array('as' => 'api.emails.viewMarketingEmail', 'uses' => 'Controllers\Api\PublicController@viewMarketingEmail'));
		
		Route::any('emails/sendMessages','Controllers\Api\MessagesController@sendMessages');
		Route::any('emails/processBulkMessages','Controllers\Api\MessagesController@processBulkMessages');
		
		Route::any('emails/sendFriendEmail','Controllers\Api\PublicController@sendFriendEmail');
		Route::any('emails/sendFriendSms','Controllers\Api\PublicController@sendFriendSms');
		
		Route::any('instances/getInstancesByLocation',array('as' => 'backend.booking.instance.location', 'uses' => 'Controllers\Api\CourseInstancesController@getInstancesByLocation'));
		Route::any('instances/getInstancesByLocationCourseAndDate','Controllers\Api\CourseInstancesController@getInstancesByLocationCourseAndDate');

		Route::get('instructors/getInstructors', array('as' => 'backend.instructors.getInstructors', 'uses' => 'Controllers\Api\InstructorsController@getInstructors'));
		Route::post('instructors/updateInstructors', array('as' => 'backend.instructors.updateInstructors', 'uses' => 'Controllers\Api\InstructorsController@updateInstructors'));
		
		Route::any('courserepeats/run', array('as' => 'api.courserepeats.run', 'uses' => 'Controllers\Api\CourseRepeatsController@run'));
		Route::any('courserepeats/updateNoShows', array('as' => 'api.courserepeats.updateNoShows', 'uses' => 'Controllers\Api\CourseRepeatsController@updateNoShows'));
		
		Route::any('certificates/createNewCustomer', array('as' => 'api.certificates.createNewCustomer', 'uses' => 'Controllers\Api\CertificatesController@createNewCustomer'));
		Route::any('certificates/updateCustomer', array('as' => 'api.certificates.updateCustomer', 'uses' => 'Controllers\Api\CertificatesController@updateCustomer'));
		Route::any('certificates/updateCustomerAndDownload', array('as' => 'api.certificates.updateCustomerAndDownload', 'uses' => 'Controllers\Api\CertificatesController@updateCustomerAndDownload'));
		Route::any('certificates/downloadCertificate/{id}', array('as' => 'api.certificates.downloadCertificate', 'uses' => 'Controllers\Api\CertificatesController@downloadCertificate'));
		Route::any('certificates/downloadClassCertificates/{id}', array('as' => 'api.certificates.downloadClassCertificates', 'uses' => 'Controllers\Api\CertificatesController@downloadClassCertificates'));
		Route::any('certificates/viewCertificate/{id}', array('as' => 'api.certificates.viewCertificate', 'uses' => 'Controllers\Api\CertificatesController@viewCertificate'));
		Route::any('certificates/download', array('as' => 'api.certificates.download', 'uses' => 'Controllers\Api\CertificatesController@download'));
		
		Route::post('documents/upload', array('as' => 'api.documents.upload', 'uses' => 'Controllers\Api\DocumentsController@upload'));
		Route::any('documents/downloadExternalDocument/{filename}', array('as' => 'api.documents.downloadExternalDocument', 'uses' => 'Controllers\Api\DocumentsController@downloadExternalDocument'));
		Route::any('documents/viewExternalDocument/{filename}', array('as' => 'api.documents.viewExternalDocument', 'uses' => 'Controllers\Api\DocumentsController@viewExternalDocument'));

		Route::get('getGiftVoucher/{id}','Controllers\Api\PublicController@getVoucher');
			
		Route::any('orders/activateOrder','Controllers\Api\OrdersController@activateOrder');
		Route::any('orders/deactivateOrder','Controllers\Api\OrdersController@deactivateOrder');
		Route::any('orders/getItems','Controllers\Api\OrdersController@getItems');
		Route::any('orders/getOrderById','Controllers\Api\OrdersController@getOrderById');
		Route::any('orders/processPaidList','Controllers\Api\OrdersController@processPaidList');
		Route::any('orders/processNotPaidList','Controllers\Api\OrdersController@processNotPaidList');
		Route::any('orders/createNewTransaction','Controllers\Api\OrdersController@createNewTransaction');
		
		Route::any('rosters/updateRoster','Controllers\Api\RostersController@updateRoster');
		
		Route::controller('calendar','Controllers\Api\CalendarController');
		Route::get('calendar/','Controllers\Api\CalendarController@index');
		
		Route::post('search/','Controllers\Api\CalendarController@getSearch');

		Route::get('vouchers/download/{id}', array('as' => 'backend.vouchers.download', 'uses' => 'Controllers\Api\VouchersController@download'));
		
		Route::any('rosters/getRosters', array('as' => 'api.rosters.getRosters', 'uses' => 'Controllers\Api\RostersController@getRosters'));
		Route::any('rosters/sendCertificatesEmail/{id}', array('as' => 'api.rosters.sendCertificatesEmail', 'uses' => 'Controllers\Api\RostersController@sendCertificatesEmail'));
		Route::any('rosters/sendCertificateToStudent/{id}', array('as' => 'api.rosters.sendCertificateToStudent', 'uses' => 'Controllers\Api\RostersController@sendCertificateToStudent'));
		
		Route::any('createagent', array('as' => 'api.createagent', 'uses' => 'Controllers\Api\PublicController@createAgent'));
		
		Route::any('createcompany', array('as' => 'api.createcompany', 'uses' => 'Controllers\Api\PublicController@createCompany'));

		Route::get('files/list/{type}', array('as' => 'api.files.list', 'uses' => 'Controllers\Api\FilesController@getFilesList'));
		Route::post('files/upload/{type}', array('as' => 'api.files.upload', 'uses' => 'Controllers\Api\FilesController@upload'));
		
		Route::any('files/downloadFile/{filename}', array('as' => 'api.files.downloadFile', 'uses' => 'Controllers\Api\PublicController@downloadFile'));
		Route::any('files/downloadAttachment/{filename}', array('as' => 'api.files.downloadFile', 'uses' => 'Controllers\Api\PublicController@downloadAttachment'));
		Route::any('files/viewFile/{filename}', array('as' => 'api.files.viewFile', 'uses' => 'Controllers\Api\PublicController@viewFile'));
		
		Route::post('customers/search', array('as' => 'api.customers.search', 'uses' => 'Controllers\Api\SearchController@searchCustomers'));
		
		Route::post('usi/verifyUsi', array('as' => 'api.usi.verifyUsi', 'uses' => 'Controllers\Api\UsiController@verifyUsi'));
		Route::post('usi/bulkVerifyUsi', array('as' => 'api.usi.bulkVerifyUsi', 'uses' => 'Controllers\Api\UsiController@bulkVerifyUsi'));
		
		Route::post('usi/createUsi', array('as' => 'api.usi.createUsi', 'uses' => 'Controllers\Api\UsiController@createUsi'));
		Route::post('usi/bulkCreateUsi', array('as' => 'api.usi.bulkCreateUsi', 'uses' => 'Controllers\Api\UsiController@bulkCreateUsi'));
		
		Route::post('usi/getStudentDetails', array('as' => 'api.usi.getStudentDetails', 'uses' => 'Controllers\Api\UsiController@getStudentDetails'));
		
		Route::get('suburbs/getSuburbs/{post_code}', array('as' => 'api.suburbs.getSuburbs', 'uses' => 'Controllers\Api\SuburbController@getSuburbs'));
		
});

Route::group(array('prefix' => 'backend'), function()
{

	# User Management
		Route::resource('users','Controllers\Backend\UsersController');
		Route::get('{userId}/delete', array('as' => 'delete/user', 'uses' => 'Controllers\Backend\UsersController@destroy'));
		Route::get('{userId}/restore', array('as' => 'restore/user', 'uses' => 'Controllers\Backend\UsersController@restore'));
		
		Route::get('instructors/roster', array('as' => 'backend.instructors.roster', 'uses' => 'Controllers\Backend\InstructorsController@roster'));
		Route::resource('instructors', 'Controllers\Backend\InstructorsController');
		Route::get('{userId}/deleting', array('as' => 'deleting/instructor', 'uses' => 'Controllers\Backend\InstructorsController@destroy'));
		Route::get('{userId}/restoring', array('as' => 'restoring/instructor', 'uses' => 'Controllers\Backend\InstructorsController@restore'));
		Route::any('instances/updtrainers', array('as' => 'backend.instances.updtrainers', 'uses' => 'Controllers\Backend\InstructorsController@updtrainers'));

	# Group Management
		Route::resource('groups', 'Controllers\Backend\GroupsController');	

	#Calendar
		Route::get('calendar/', array('as' => 'backend.calendar', 'uses' => 'Controllers\Backend\CalendarController@index'));
		Route::any('calendar/search', array('as' => 'backend.calendar.search', 'uses' => 'Controllers\Backend\CalendarController@search'));
		Route::any('calendar/olgrClasslist/{id}/{type}/{csv?}', array('as' => 'backend.calendar.olgrClasslist', 'uses' => 'Controllers\Backend\CalendarController@getOlgrClassList'));
		Route::any('calendar/classolgrlist/{id}/{type}/{csv?}', array('as' => 'backend.calendar.classolgrlist', 'uses' => 'Controllers\Backend\CalendarController@getClassOlgrList'));
		Route::any('calendar/classlist/{id}/{type}/{csv?}', array('as' => 'backend.calendar.classlist', 'uses' => 'Controllers\Backend\CalendarController@getClassOlgrList'));
		
		//Route::any('calendar/classlist/{id}/{type}', array('as' => 'backend.calendar.classlist', 'uses' => 'Controllers\Backend\CalendarController@getClassList'));
		Route::any('calendar/classlistupdate/{id}/{type}', array('as' => 'backend.calendar.classlistupdate', 'uses' => 'Controllers\Backend\CalendarController@getClassListUpdate'));
		Route::any('calendar/reconcile/{id}/{type}', array('as' => 'backend.calendar.reconcile', 'uses' => 'Controllers\Backend\CalendarController@getReconcile'));
		Route::any('calendar/trainers', array('as' => 'backend.calendar.trainers', 'uses' => 'Controllers\Backend\CalendarController@trainers'));
		Route::any('calendar/agents', array('as' => 'backend.calendar.agents', 'uses' => 'Controllers\Backend\CalendarController@agents'));
		
	
	#Bookings	
		Route::get('booking', array('as' => 'backend.booking', 'uses' => 'Controllers\Backend\BookingsController@newBooking'));
		Route::get('booking/newBooking', array('as' => 'backend.booking.newBooking', 'uses' => 'Controllers\Backend\BookingsController@newBooking'));
		Route::get('booking/newGroupBooking', array('as' => 'backend.booking.newGroupBooking', 'uses' => 'Controllers\Backend\BookingsController@newGroupBooking'));
		Route::get('booking/newPurchase', array('as' => 'backend.booking.newPurchase', 'uses' => 'Controllers\Backend\BookingsController@newPurchase'));
		Route::any('booking/search', array('as' => 'backend.booking.search', 'uses' => 'Controllers\Backend\BookingsController@findBooking'));
		Route::any('booking/search/{id}', array('as' => 'backend.booking.searchByOrderId', 'uses' => 'Controllers\Backend\BookingsController@findBookingByOrderId'));

		Route::any('booking/deactivate', array('as' => 'backend.booking.deactivate', 'uses' => 'Controllers\Backend\BookingsController@deactivate'));
		Route::any('booking/reactivate', array('as' => 'backend.booking.reactivate', 'uses' => 'Controllers\Backend\BookingsController@reactivate'));

	# Purchases
		Route::get('booking/newPurchase', array('as' => 'backend.booking.newPurchase', 'uses' => 'Controllers\Backend\BookingsController@newPurchase'));
		Route::resource('purchases', 'Controllers\Backend\PurchasesController');

	# Agents
		Route::resource('agents', 'Controllers\Backend\AgentsController');
		Route::resource('companies', 'Controllers\Backend\CompaniesController');
		Route::resource('suppliers', 'Controllers\Backend\SuppliersController');
		
	# Customers
		Route::get('customers/{filename}/delete', array('as' => 'customers/delete/customer', 'uses' => 'Controllers\Backend\CustomersController@destroy'));
		Route::post('customers/merge', array('as' => 'customers/merge', 'uses' => 'Controllers\Backend\CustomersController@merge'));
		Route::resource('customers', 'Controllers\Backend\CustomersController');
			
	# Locations
		Route::resource('locations', 'Controllers\Backend\LocationsController');

	# Courses
		Route::resource('courses', 'Controllers\Backend\CoursesController');
		
		//Route::get('courserepeats/run/{id}', array('as' => 'backend.courserepeats.run', 'uses' => 'Controllers\Backend\CourseRepeatsController@run'));
		Route::resource('courserepeats','Controllers\Backend\CourseRepeatsController');
		Route::resource('coursebundles','Controllers\Backend\CourseBundlesController');
		Route::resource('instances', 'Controllers\Backend\CourseInstancesController');
		Route::resource('groupinstances', 'Controllers\Backend\GroupBookingsController');
		Route::resource('rosters', 'Controllers\Backend\RostersController');
		
		Route::any('certificates/list/{id}/{type}', array('as' => 'backend.certificates.list', 'uses' => 'Controllers\Backend\CertificatesController@getCertificatesList'));
		Route::post('certificates/save', array('as' => 'backend.certificates.save', 'uses' => 'Controllers\Backend\CertificatesController@save'));
		Route::get('certificates/download/{id}', array('as' => 'backend.certificates.download', 'uses' => 'Controllers\Api\CertificatesController@downloadCertificate'));
		Route::resource('certificates', 'Controllers\Backend\CertificatesController');

	# Invoicing
		Route::resource('orders', 'Controllers\Backend\OrdersController');
		Route::resource('items', 'Controllers\Backend\ItemsController');
		Route::get('invoices/email/{id}/{recreate}', array('as' => 'backend.invoices.email', 'uses' => 'Controllers\Backend\InvoicesController@email'));
		Route::get('invoices/download/{id}/{recreate}', array('as' => 'backend.invoices.download', 'uses' => 'Controllers\Backend\InvoicesController@download'));
		Route::resource('invoices', 'Controllers\Backend\InvoicesController');
		Route::resource('payments', 'Controllers\Backend\PaymentsController');
		Route::get('vouchers/download/{id}', array('as' => 'backend.vouchers.download', 'uses' => 'Controllers\Api\VouchersController@download'));
		Route::resource('vouchers', 'Controllers\Backend\VouchersController');

	# Messaging
		Route::resource('messagetypes', 'Controllers\Backend\MessageTypesController');
		Route::any('messages/clone/{id}', array('as' => 'backend.messages.clone', 'uses' => 'Controllers\Backend\MessagesController@CloneMessage'));
		Route::resource('messages', 'Controllers\Backend\MessagesController');
		Route::any('marketing/clone/{id}', array('as' => 'backend.marketing.clone', 'uses' => 'Controllers\Backend\MarketingController@CloneMessage'));
		Route::resource('marketing', 'Controllers\Backend\MarketingController');
		Route::resource('attachments', 'Controllers\Backend\AttachmentsController');

	# Others
		Route::resource('statuses', 'Controllers\Backend\StatusesController');
		Route::resource('payment_methods', 'Controllers\Backend\PaymentMethodsController');
		Route::resource('products', 'Controllers\Backend\ProductsController');
		Route::resource('referrers', 'Controllers\Backend\ReferrersController');

	# Reports
		Route::any('reports/agent/{csv?}', array('as' => 'backend.reports.agent', 'uses' => 'Controllers\Backend\ReportsController@agent'));
		Route::any('reports/financial', array('as' => 'backend.reports.financial', 'uses' => 'Controllers\Backend\ReportsController@financial'));
		Route::any('reports/financialentries', array('as' => 'backend.reports.financialentries', 'uses' => 'Controllers\Backend\ReportsController@financialentries'));
		Route::any('reports/owing/{owing_date}', array('as' => 'backend.reports.owing', 'uses' => 'Controllers\Backend\ReportsController@owing_info'));
		Route::any('reports/transactions', array('as' => 'backend.reports.transactions', 'uses' => 'Controllers\Backend\ReportsController@transactions'));
		Route::any('reports/staff_financial', array('as' => 'backend.reports.staff_financial', 'uses' => 'Controllers\Backend\ReportsController@staff_financial'));
		Route::any('reports/staff_sales', array('as' => 'backend.reports.staff_sales', 'uses' => 'Controllers\Backend\ReportsController@staff_sales'));
		Route::any('reports/trainerrosters', array('as' => 'backend.reports.trainerrosters', 'uses' => 'Controllers\Backend\ReportsController@trainerrosters'));
		Route::any('reports/exportmyob', array('as' => 'backend.reports.exportmyob', 'uses' => 'Controllers\Backend\ReportsController@exportmyob'));
		Route::any('reports/downloadmyob', array('as' => 'backend.reports.downloadmyob', 'uses' => 'Controllers\Backend\ReportsController@downloadmyob'));
		Route::any('reports/removemyob', array('as' => 'backend.reports.removemyob', 'uses' => 'Controllers\Backend\ReportsController@removemyob'));
		Route::any('reports/dashboard', array('as' => 'backend.reports.dashboard', 'uses' => 'Controllers\Backend\ReportsController@dashboard'));
		//Route::resource('reports', 'Controllers\Backend\ReportsController');

	# CMS
		Route::any('content/content/{id}', array('as' => 'backend.content.content', 'uses' => 'Controllers\Backend\ContentController@content'));
		Route::any('content/editblock/{page_id}/{block_type}', array('as' => 'backend.content.editblock', 'uses' => 'Controllers\Backend\ContentController@editblock'));
		Route::any('cms/clone/{id}', array('as' => 'backend.cms.clone', 'uses' => 'Controllers\Backend\CmsController@ClonePage'));
		Route::resource('cms', 'Controllers\Backend\CmsController');
		Route::resource('content', 'Controllers\Backend\ContentController');
		Route::resource('resources', 'Controllers\Backend\ResourceController');

	#AVETMISS
		Route::any('avetmiss/export', array('as' => 'backend.avetmiss.export', 'uses' => 'Controllers\Backend\AvetmissController@export'));
		Route::any('avetmiss/downloadfile', array('as' => 'backend.avetmiss.downloadfile', 'uses' => 'Controllers\Backend\AvetmissController@downloadfile'));
		Route::any('avetmiss/removefile', array('as' => 'backend.avetmiss.removefile', 'uses' => 'Controllers\Backend\AvetmissController@removefile'));
		Route::any('avetmiss/inport', array('as' => 'backend.avetmiss.import', 'uses' => 'Controllers\Backend\AvetmissController@export'));

	# Dashboard
		//Route::get('/', array('as' => 'backend', 'uses' => 'Controllers\Backend\CalendarController@index'));
		Route::get('/', array('as' => 'backend', 'uses' => 'Controllers\Backend\CalendarController@index'));
		
		Route::get('/cache/flush', array('as' => 'backend.cache.flush', 'uses' => 'Controllers\Backend\CalendarController@flushCache'));

		Route::get('/dailytasks', array('as' => 'backend.dailytasks', 'uses' => 'Controllers\Backend\DailyTaskController@index'));
		Route::get('/dailytasks/run', array('as' => 'backend.dailytasks.run', 'uses' => 'Controllers\Backend\DailyTaskController@runDailyTasks'));
		Route::get('/usitask', array('as' => 'backend.usitask', 'uses' => 'Controllers\Backend\DailyTaskController@usitask'));
		Route::get('/usitask/run', array('as' => 'backend.usitask.run', 'uses' => 'Controllers\Backend\DailyTaskController@runUsiTask'));
		Route::get('/hourlytasks/run', array('as' => 'backend.hourlytasks.run', 'uses' => 'Controllers\Backend\DailyTaskController@runHourlyTasks'));
		Route::post('/tasks/run', array('as' => 'backend.tasks.run', 'uses' => 'Controllers\Backend\DailyTaskController@runTask'));


});

/*

ONLINE COURSES

*/
Route::group(array('domain' => 'online.coffeeschool.com.local'), function()
	{
		Route::get('/', array('as' => 'online', 'uses' => 'Controllers\Online\HomeController@index'));
		Route::get('/home', array('as' => 'online.home', 'uses' => 'Controllers\Online\HomeController@index'));
		Route::get('/register', array('as' => 'online.register', 'uses' => 'Controllers\Online\HomeController@bookings'));
		
		Route::get('/courses', array('as' => 'online.courses', 'uses' => 'Controllers\Online\CoursesController@index'));
		Route::get('/course/{slug}', array('as' => 'online.course', 'uses' => 'Controllers\Online\CoursesController@getCourse'));

		Route::get('/module/{id}', array('as' => 'online.module', 'uses' => 'Controllers\Online\CoursesController@getModule'));

		Route::get('/step/{id}', array('as' => 'online.step', 'uses' => 'Controllers\Online\CoursesController@getStep'));
		Route::post('/answer', array('as' => 'online.answer', 'uses' => 'Controllers\Online\CoursesController@postAnswer'));

	});

Route::group(array('prefix' => 'online'), function()
	{
		Route::get('/', array('as' => 'online', 'uses' => 'Controllers\Online\HomeController@index'));
		Route::get('/home', array('as' => 'online.home', 'uses' => 'Controllers\Online\HomeController@index'));
		Route::get('/register', array('as' => 'online.register', 'uses' => 'Controllers\Online\HomeController@bookings'));
		Route::get('/clear/history', array('as' => 'online.clear.history', 'uses' => 'Controllers\Online\HomeController@clearHistory'));
		
		Route::get('/populate/course/{roster_id}', array('as' => 'online.populate.course', 'uses' => 'Controllers\Online\HomeController@populateHistory'));

		Route::get('/assessment/{course_id}/{order_id?}', array('as' => 'online.assessment', 'uses' => 'Controllers\Online\BookingController@getAssessment'));
		Route::get('/booking/{id}', array('as' => 'online.booking', 'uses' => 'Controllers\Online\BookingController@getBookingDetails'));

		Route::get('/thankyou/{order_id}/{student_id?}', array('as' => 'online.thankyou', 'uses' => 'Controllers\Online\BookingController@thankyou'));
		
		Route::get('/course/results/{id}', array('as' => 'online.course.results', 'uses' => 'Controllers\Online\CoursesController@displayCourseResults'));
		Route::get('/course/{slug}', array('as' => 'online.course', 'uses' => 'Controllers\Online\CoursesController@getCourse'));
		Route::get('/module/{id}', array('as' => 'online.module', 'uses' => 'Controllers\Online\CoursesController@getModule'));
		Route::get('/module/results/{id}', array('as' => 'online.module.results', 'uses' => 'Controllers\Online\CoursesController@displayModuleResults'));
		Route::get('/step/results/{id}', array('as' => 'online.step.results', 'uses' => 'Controllers\Online\CoursesController@displayStepResults'));
		Route::get('/step/{id}', array('as' => 'online.step', 'uses' => 'Controllers\Online\CoursesController@getStep'));
		Route::post('/answer', array('as' => 'online.answer', 'uses' => 'Controllers\Online\CoursesController@postAnswer'));
		

		Route::get('{id}/module_delete', array('as' => 'delete/module', 'uses' => 'Controllers\Online\ModulesController@destroy'));
		Route::resource('modules', 'Controllers\Online\ModulesController');
		
		Route::get('{id}/step_delete', array('as' => 'delete/step', 'uses' => 'Controllers\Online\StepsController@destroy'));
		Route::resource('steps', 'Controllers\Online\StepsController');
		
		Route::get('{id}/question_delete', array('as' => 'delete/question', 'uses' => 'Controllers\Online\QuestionsController@destroy'));
		Route::resource('questions', 'Controllers\Online\QuestionsController');
		
		Route::get('{id}/answer_delete', array('as' => 'delete/answer', 'uses' => 'Controllers\Online\AnswersController@destroy'));
		Route::resource('answers', 'Controllers\Online\AnswersController');

		Route::resource('courses', 'Controllers\Online\CoursesController');

		Route::resource('rosters', 'Controllers\Online\RostersController');
		Route::get('progress/{roster_id}', array('as' => 'online.progress', 'uses' => 'Controllers\Online\RostersController@displayProgress'));
		
		Route::get('/courses', array('as' => 'online.courses', 'uses' => 'Controllers\Online\HomeController@index'));

		Route::get('/profile', array('as' => 'online.profile', 'uses' => 'Controllers\Account\ProfileController@getOnlineProfile'));
		Route::post('/profile', 'Controllers\Account\ProfileController@postOnlineProfile');
		
		Route::get('logout', array('as' => 'online.logout', 'uses' => 'AuthController@getOnlineLogout'));
		
		Route::get('contact', array('as' => 'online.contact', 'uses' => 'Controllers\Online\HomeController@getContact'));
		Route::post('contact', array('as' => 'online.contact', 'uses' => 'Controllers\Online\HomeController@postContact'));


	});


/*
|--------------------------------------------------------------------------
| Authentication and Authorization Routes
|--------------------------------------------------------------------------
|
*/

Route::group(array('prefix' => 'auth'), function()
	{

	# Login
	Route::get('signin', array('as' => 'signin', 'uses' => 'AuthController@getSignin'));
	Route::post('signin', 'AuthController@postSignin');

	# Register
	Route::get('signup', array('as' => 'signup', 'uses' => 'AuthController@getSignup'));
	Route::post('signup', 'AuthController@postSignup');

	# Account Activation
	Route::get('activate/{activationCode}', array('as' => 'activate', 'uses' => 'AuthController@getActivate'));

	# Forgot Password
	Route::get('forgot-password', array('as' => 'forgot-password', 'uses' => 'AuthController@getForgotPassword'));
	Route::post('forgot-password', 'AuthController@postForgotPassword');

	# Forgot Password Confirmation
	Route::get('forgot-password/{passwordResetCode}', array('as' => 'forgot-password-confirm', 'uses' => 'AuthController@getForgotPasswordConfirm'));
	Route::post('forgot-password/{passwordResetCode}', 'AuthController@postForgotPasswordConfirm');

	# Logout
	Route::get('logout', array('as' => 'logout', 'uses' => 'AuthController@getLogout'));

});

/*
|--------------------------------------------------------------------------
| Account Routes
|--------------------------------------------------------------------------
|
*/

Route::group(array('prefix' => 'account'), function()
{

	# Account Dashboard
	Route::get('/', array('as' => 'account', 'uses' => 'Controllers\Account\DashboardController@getIndex'));

	# Profile
	Route::get('profile', array('as' => 'profile', 'uses' => 'Controllers\Account\ProfileController@getIndex'));
	Route::post('profile', 'Controllers\Account\ProfileController@postIndex');

	# Change Password
	Route::get('change-password', array('as' => 'change-password', 'uses' => 'Controllers\Account\ChangePasswordController@getIndex'));
	Route::post('change-password', 'Controllers\Account\ChangePasswordController@postIndex');

	# Change Email
	Route::get('change-email', array('as' => 'change-email', 'uses' => 'Controllers\Account\ChangeEmailController@getIndex'));
	Route::post('change-email', 'Controllers\Account\ChangeEmailController@postIndex');

});



/*
|--------------------------------------------------------------------------
| RSA SYDNEY Routes
|--------------------------------------------------------------------------
|
*/

Route::group(array('prefix' => 'agent'), function()
{
		
		//Route::get('/{agent}/{location}/share/{order_id}/{student_id?}', array('as' => 'agent.share', 'uses' => 'HomeController@share'));
		//Route::get('/{agent}/{location}/thankyou/{order_id}/{student_id?}', array('as' => 'agent.thankyou', 'uses' => 'HomeController@thankyou'));
		//Route::get('/{agent}/{location}/cancelled/{order_id}', array('as' => 'agent.cancelled', 'uses' => 'HomeController@cancelled'));
		//Route::any('/{agent}/{l}/enrolment/form/{o_id?}/{s_id?}', array('as' => 'agent.enrolment.form', 'uses' => 'HomeController@enrolment'));
	
		Route::get('/{agent}/{location}/{action?}/{order_id?}/{student_id?}/{extra?}', array('as' => 'agent', 'uses' => 'HomeController@agents'));

});



/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/captcha', function() {	return \Captcha::create(); });

Route::get('/usi_privacy_notice', array('as' => 'usi_privacy_notice', 'uses' => 'HomeController@usi_privacy_notice'));
Route::get('/student_handbook', array('as' => 'participant_handbook', 'uses' => 'HomeController@student_handbook'));
Route::get('/participant_handbook', array('as' => 'participant_handbook', 'uses' => 'HomeController@participant_handbook'));
Route::get('/privacy_terms_conditions', array('as' => 'privacy_terms_conditions', 'uses' => 'HomeController@privacy_terms_conditions'));

Route::get('/share/{order_id}/{student_id?}', array('as' => 'share', 'uses' => 'HomeController@share'));
Route::get('/thankyou/{order_id}/{student_id?}', array('as' => 'thankyou', 'uses' => 'HomeController@thankyou'));
Route::get('/cancelled/{order_id}', array('as' => 'cancelled', 'uses' => 'HomeController@cancelled'));
Route::any('/enrolment/form/{o_id?}/{s_id?}', array('as' => 'enrolment.form', 'uses' => 'HomeController@enrolment'));

//aliases
Route::get('/{path?}/{name?}/{location?}', array('as' => 'catchall', 'uses' => 'HomeController@catchall'));


//Route::get('/sydney', array('as' => 'sydney', 'uses' => 'HomeController@aliases'));
//Route::get('/melbourne', array('as' => 'melbourne', 'uses' => 'HomeController@aliases'));
//Route::get('/brisbane', array('as' => 'brisbane', 'uses' => 'HomeController@aliases'));
//Route::get('/perth', array('as' => 'perth', 'uses' => 'HomeController@aliases'));
//Route::get('/parramatta', array('as' => 'parramatta', 'uses' => 'HomeController@aliases'));
//Route::get('/penrith', array('as' => 'penrith', 'uses' => 'HomeController@aliases'));

//Route::get('/rsacourse', array('as' => 'rsacourse', 'uses' => 'HomeController@aliases'));
//Route::get('/rcgcourse', array('as' => 'rcgcourse', 'uses' => 'HomeController@aliases'));
//Route::get('/coffeecourse', array('as' => 'coffeecourse', 'uses' => 'HomeController@aliases'));
//Route::get('/cocktailscourse', array('as' => 'cocktailscourse', 'uses' => 'HomeController@aliases'));
//Route::get('/foodhygienecourse', array('as' => 'foodhygienecourse', 'uses' => 'HomeController@aliases'));
//Route::get('/foodsafetycourse', array('as' => 'foodsafetycourse', 'uses' => 'HomeController@aliases'));
//Route::get('/baristacourse', array('as' => 'baristacourse', 'uses' => 'HomeController@aliases'));
//Route::get('/coffeeartcourse', array('as' => 'coffeeartcourse', 'uses' => 'HomeController@aliases'));

//Route::get('/rsacourse/{location?}', array('as' => 'rsacourse', 'uses' => 'HomeController@aliases'));
//Route::get('/rcgcourse/{location?}', array('as' => 'rcgcourse', 'uses' => 'HomeController@aliases'));
//Route::get('/coffeecourse/{location?}', array('as' => 'coffeecourse', 'uses' => 'HomeController@aliases'));
//Route::get('/cocktailscourse/{location?}', array('as' => 'cocktailscourse', 'uses' => 'HomeController@aliases'));
//Route::get('/foodhygienecourse/{location?}', array('as' => 'foodhygienecourse', 'uses' => 'HomeController@aliases'));
//Route::get('/foodsafetycourse/{location?}', array('as' => 'foodsafetycourse', 'uses' => 'HomeController@aliases'));
//Route::get('/baristacourse/{location?}', array('as' => 'baristacourse', 'uses' => 'HomeController@aliases'));
//Route::get('/coffeeartcourse/{location?}', array('as' => 'coffeeartcourse', 'uses' => 'HomeController@aliases'));
//

//aliases

Route::get('/specials', array('as' => 'specials', 'uses' => 'HomeController@specials'));
Route::get('/bookings', array('as' => 'bookings', 'uses' => 'HomeController@bookings'));
Route::get('/vouchers', array('as' => 'vouchers', 'uses' => 'HomeController@vouchers'));

Route::post('/contact/form', array('as' => 'contact.form', 'uses' => 'HomeController@contact'));


Route::get('/', array('as' => 'home', 'uses' => 'HomeController@home'));
//Route::get('/content/{name}', array('as' => 'content', 'uses' => 'HomeController@content'));
//Route::get('/content/{name}/{location}', array('as' => 'content', 'uses' => 'HomeController@content'));
//Route::post('/contact/form', array('as' => 'contact.form', 'uses' => 'HomeController@contact'));

//Route::get('/specials/{location}', array('as' => 'specials', 'uses' => 'HomeController@specials'));
//Route::get('/bookings/{location}', array('as' => 'bookings', 'uses' => 'HomeController@bookings'));
//Route::get('/vouchers/{location}', array('as' => 'vouchers', 'uses' => 'HomeController@vouchers'));




Route::post('/queue/sendmessage', function() 
	{
		\Log::info("Queue Callback arrived");
		return Queue::marshal();
	});

Route::post('/queue/processmessage', function() 
	{
		\Log::info("Queue Callback arrived for processing");
		return Queue::marshal();
	});
