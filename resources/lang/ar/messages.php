<?php

return [
	
	'success' => [
		'login' => 'تم تسجيل الدخول بنجاح',
		'success' => 'نجاح',
		'signup' => 'الاشتراك بنجاح',
		'update' => 'تم التحديث بنجاح',
		'unsuccess' => 'غير ناجح',
		'logout' => 'تم الخروج بنجاح',
		'review_published' => 'تم نشر المراجعة بنجاح',
		'review_un_published' => 'مراجعة غير المنشورة بنجاح',
		'appointment_accepted' => 'تم قبول الموعد بنجاح',
		'appointment_rejected' => 'تم رفض التعيين بنجاح',
		'password_updated' => 'تم تحديث كلمة السر بنجاح',
		'appointment_rescheduled' => 'تم إعادة جدولة الموعد بنجاح',
		'appointment_scheduled' => 'تم تعيين الموعد بنجاح',
		'NO_DATA_FOUND' => 'لاتوجد بيانات'
	],

	'error' => [
		'insert' => 'حدث خطأ في إنشاء السجل',
		'incorrect_old_password' => 'كلمة سر قديمة ليست صحيحة'
	],


	'statusCode' => [
		'ALREADY_EXIST' => '422',
		'PARAMETER_MISSING' => '422',
		'INVALID_ACCESS_TOKEN' => '401',
		'INVALID_CREDENTIAL' => '403',
		'ACTION_COMPLETE' => '200',
		'CREATE' => '201',
		'NO_DATA_FOUND' => '204',
		'IMAGE_FILE_MISSING' => '422',
		'SHOW_ERROR_MESSAGE' => '400',
		'ERROR_IN_EXECUTION' => '404',
		'BAD_REQUEST' => '500'
	],

	/*'required' => [
		'accessToken' => 'رمز الوصول المطلوب',
		'user_id' => 'مطلوب معرف المستخدم',
		'requestToUserId' => 'مطلوب ريكتوسريد',
		'email' => '',
		'password' => '',
		'device_token' => '',
		'device_type' => '',
		'mobile' => '',
		'country_code' => '',
		'user_type' => '',
		'fullName' => '',
		'otp' => '',
		'key' => '',
		'old_password' => '',
		'new_password' => '',
		'isChangedCountryCode' => '',
		'isChangedMobile' => '',
		'profileImage' => '',
		'qualification' => '',
		'experience' => '',
		'workingPlace' => '',
		'motherLanguage' => '',
		'medical_licence_number' => '',
		'issuing_country' => '',
		'speciality_id' => '',
		'days' => '',
		'timeslots' => '',
		'review_id' => '',
		'acceptOrReject' => '',
		'date' => '',
		'page_number' => '',
		'patient_id' => '',
		'doctor_id' => '',
	],*/




	'invalid' =>[
		'number' => 'رقم غير صالح',
		'detail' => 'تفاصيل غير صالحة',
		'request' => 'طلب غير صالح',
		'credentials' => 'بيانات الاعتماد غير صالحة',
		'accessToken' => 'تصريح الدخول غير صالح',
		'OTP' => 'مكتب المدعي العام غير صالح',
		'appointment_date' => 'لا يمكنك حجز موعد للأيام السابقة',
		'doctor_not_available_at_this_time_slot' => 'الطبيب غير متوفر في هذه الفتحة الزمنية',
		'appointment_expired' => 'انتهت صلاحية التعيين'
	],

	'same' => [
		'same_number' => 'لقد أدخلت الرقم نفسه',
		'country_code' => 'نفس رمز البلد كآخر'
	],

	'Already_Busy_Time_Slot_With_Other_Patient' => 'بالفعل فتحة الوقت مشغول مع مريض آخر',

	'Patient_Already_Booked_appointment' => 'لقد حجزت مسبقا موعدك',
	'Appointment_already_booked_at_this_time_slot_for_this_patient' => 'حجز حجز بالفعل في هذا الوقت فتحة لهذا المريض',


];
