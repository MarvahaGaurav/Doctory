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
		'review_deleted' => 'review deleted.',
		'appointment_accepted' => 'تم قبول الموعد بنجاح',
		'appointment_rejected' => 'تم رفض التعيين بنجاح',
		'password_updated' => 'تم تحديث كلمة السر بنجاح',
		'appointment_rescheduled' => 'تم إعادة جدولة الموعد بنجاح',
		'appointment_scheduled' => 'تم تعيين الموعد بنجاح',
		'NO_DATA_FOUND' => 'لاتوجد بيانات',
		'otp_resend' => 'إعادة إرسال أوتب بنجاح.',
		'otp_verified' => 'تم التحقق من مكتب المدعي العام بنجاح.',
		'email_forget_otp' => 'إرسال رمز بنجاح.',
		'reset_password' => 'تم إعادة تعيين كلمة المرور بنجاح. الرجاء تسجيل الدخول مرة أخرى.',
		'mobile_changed' => 'تم تغيير رقم الجوال بنجاح',
		'complete_profile' => 'تم إنشاء الملف الشخصي',
		'QA_added' => 'تمت إضافة التأهيل بنجاح.',
		'QA_deleted' => 'تم حذف التأهل بنجاح.',
		'QA_already_exist' => 'المؤهلات موجودة بالفعل.',
		'mother_language_added' => 'تمت إضافة لغة الأم بنجاح.',
		'mother_language_already_exist' => 'لغة الأم موجودة بالفعل.',
		'ML_deleted' => 'اللغة الأم المحذوفة.',
		'speciality_added' => 'تمت إضافة التخصص بنجاح.',
		'SP_deleted' => 'تم حذف التخصص.',
		'speciality_already_exist' => 'التخصص موجود بالفعل.',
		'patient_unblocked' => 'تم رفع الحظر عن المريض بنجاح.',
		'patient_blocked' => 'تم منع المريض بنجاح.',
		'docotr_approved' => 'وافق الطبيب بنجاح.',
		'speciality_updated' => 'تم تحديث التخصص بنجاح.',
		'qualificationy_updated' => 'تم تحديث التأهيل بنجاح.',
		'mother_language_updated' => 'تم تحديث لغة الأم بنجاح',
		'Admin_profile_updated' => 'تم تحديث الملف الشخصي للمشرف بنجاح.',
		'Image_uploaded_success' => 'تم تحميل الصورة بنجاح.',
		'Video_uploaded_success' => 'تم تحميل الفيديو بنجاح.',
		'Audio_uploaded_success' => 'تم تحميل الصوت بنجاح.',
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

	'appointment_status' => [
		'PENDING' => 'قيد الانتظار',
		'ACCEPTED_BY_DOCTOR' => 'تم قبول موعدك',
		'REJECTED_BY_DOCTOR' => 'رفضت من قبل الطبيب',
		'CANCELED_BY_DOCTOR' => 'ألغى من قبل الطبيب',
		'CANCELED_BY_PATIENT' => 'ألغيت من قبل المريض',
		'RESCHEDULED_BY_PATIENT' => 'إعادة جدولة من قبل المريض',
		'RESCHEDULED_BY_DOCTOR' => 'إعادة جدولة من قبل الطبيب',
		'RESCHEDULED_ACCEPTED_BY_PATIENT' => 'إعادة جدولة التعيين قبلت من قبل المريض',
		'RESCHEDULED_REJECTED_BY_PATIENT' => 'إعادة جدولة موعد رفض من قبل المريض',
		'RESCHEDULED_ACCEPTED_BY_DOCTOR' => 'إعادة تحديد الموعد قبله الطبيب',
		'RESCHEDULED_REJECTED_BY_DOCTOR' => 'إعادة جدولة موعد رفضه الطبيب',
		'COMPLETED' => 'منجز',
		'Appointment_Cancelled_By_Patient' => 'تم إلغاء الموعد بنجاح.',
		'Appointment_Cancelled_By_Doctor' => 'تم إلغاء الموعد بنجاح.'
	],


	'notification_messages' => [
		'PENDING' => 'قيد الانتظار',
		'ACCEPTED_BY_DOCTOR' => 'تم قبول موعدك.',
		'REJECTED_BY_DOCTOR' => 'تم رفض موعدك.',
		'CANCELED_BY_DOCTOR' => 'تم إلغاء موعدك.',
		'CANCELED_BY_PATIENT' => 'تم إلغاء موعدك.',
		'RESCHEDULED_BY_PATIENT' => 'لديك طلب إعادة جدولة جديد موعد.',
		'RESCHEDULED_BY_DOCTOR' => 'لديك طلب إعادة جدولة جديد موعد.',
		'RESCHEDULED_ACCEPTED_BY_PATIENT' => 'تم قبول طلب إعادة الجدولة.',
		'RESCHEDULED_REJECTED_BY_PATIENT' => 'تم رفض طلب إعادة الجدولة.',
		'RESCHEDULED_ACCEPTED_BY_DOCTOR' => 'تم قبول طلب إعادة الجدولة.',
		'RESCHEDULED_REJECTED_BY_DOCTOR' => 'تم رفض طلب إعادة الجدولة.',
		'COMPLETED' => 'منجز',
		'Appointment_Cancelled_By_Patient' => 'الموعد ألغى من قبل المريض.',
		'Appointment_Cancelled_By_Doctor' => 'تم إلغاء الموعد بنجاح.',
		'Appointment_Transfered_By_Doctor' => 'تم نقل موعدك إلى طبيب آخر.',
		'Scheduled_Appointment' => 'لديك طلب موعد جديد.',
		'Patient_Post_Review_To_Doctor' => 'لديك مراجعة جديدة.',
		'Doctor_Aprroved_By_Admin' => 'تمت الموافقة على ملفك الشخصي من المسؤول.',
		'Doctor_Have_Appointment_After_Fifteen_Minutes' => 'موعدك على وشك البدء.',
		'Patient_Have_Appointment_After_Fifteen_Minutes' => 'موعدك على وشك البدء.',
		'Extand_Chat_Notification' => 'لقد تم تمديد موعدك.',
		'Appointment_Completed' => 'لقد أكمل الطبيب موعدك. يمكنك تقديم رأيك.',
		'Appointment_Started' => 'موعدك بدأ.',
		'cancel_the_transfer_request' => 'لديك إلغاء طلب موعد النقل الخاص بك. لديك بالفعل استرداد في محفظتك.',
	],
	

	'notification_status_codes' => [
		'Rescheduled_Appointment' => '1',
		'Scheduled_Appointment' => '2',
		'Rescheduled_Appointment_Accepted_By_Patient' => '3',
		'Rescheduled_Appointment_Accepted_By_Doctor' => '4',
		'Rescheduled_Appointment_Rejected_By_Patient' => '5',
		'Rescheduled_Appointment_Rejected_By_Doctor' => '6',
		'Appointment_Rescheduled_By_Patient' => '7',
		'Appointment_Rescheduled_By_Doctor' => '8',
		'Appointment_Accepted_By_Doctor' => '9',
		'Appointment_Rejected_By_Doctor' => '10',
		'Appointment_Cancelled_By_Patient' => '11',
		'Appointment_Cancelled_By_Doctor' => '12',
		'Appointment_Transfered_By_Doctor' => '13',
		'Patient_Post_Review_To_Doctor' => '14',
		'Doctor_Aprroved_By_Admin' => '15',
		'Doctor_Have_Appointment_After_Fifteen_Minutes' => '16',
		'Patient_Have_Appointment_After_Fifteen_Minutes' => '17',
		'Extand_Chat_Notification' => '18',
		'Appointment_Completed' => '19',
		'cancel_the_transfer_request' => '20',
	],

	'notification_type' => [
		'Extend_Chat' => '2',
		'Appointment_Completed' => '3',
		'transferAppointmentByDoctor' => '4'
	],

	'required' => [
		'accessToken' => 'رمز الوصول المطلوب.',
		'user_id' => 'مطلوب معرف المستخدم.',
		'requestToUserId' => 'مطلوب ريكتوسريد',
	],

	'invalid' =>[
		'number' => 'رقم غير صالح',
		'detail' => 'تفاصيل غير صالحة',
		'request' => 'طلب غير صالح',
		'credentials' => 'بيانات الاعتماد غير صالحة',
		'accessToken' => 'تصريح الدخول غير صالح',
		'OTP' => 'مكتب المدعي العام غير صالح',
		'appointment_date' => 'لا يمكنك حجز موعد للأيام السابقة',
		'doctor_not_available_at_this_time_slot' => 'الطبيب غير متوفر في هذه الفتحة الزمنية',
		'appointment_expired' => 'انتهت صلاحية التعيين',
		'invalid_email_match' => "Your entered email dosen't match with your email."
	],

	'same' => [
		'same_number' => 'لقد أدخلت الرقم نفسه',
		'country_code' => 'نفس رمز البلد كآخر'
	],

	'Already_Busy_Time_Slot_With_Other_Patient' => 'بالفعل فتحة الوقت مشغول مع مريض آخر',

	'Patient_Already_Booked_appointment' => 'لقد حجزت مسبقا موعدك',
	'Appointment_already_booked_at_this_time_slot_for_this_patient' => 'حجز حجز بالفعل في هذا الوقت فتحة لهذا المريض',
	'Wait_For_Approval_From_Admin' =>'يرجى الانتظار للموافقة من المشرف',
	'Account_blocked_Patient' => 'تم حظر الحساب من قبل المشرف',

	'invalid_email_dr' => 'There is no such Doctor has registered with us.',
	'invalid_email_pt' => 'There is no such Patient has registered with us.',

];
