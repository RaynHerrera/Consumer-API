<?php

namespace App\Imports;

use DateTime;
use Carbon\Carbon;
use App\Models\Extension\Company;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Extension\CompanyUrlName;
use App\Models\Extension\PrivacyStatement;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Models\Import\HistoryHelpPrivacystatement;

class CustomersImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $key=>$row) {
            if ($row['company_name']) {
                // log::alert($row['url']);
                // log::alert(parse_url($row['url'], PHP_URL_HOST));
                // log::alert("https://".parse_url($row['url'], PHP_URL_HOST));
                // log::alert(parse_url("https://tjmaxx.tjx.com/store/index.jsp", PHP_URL_HOST));
                // log::alert(parse_url("https://www.deere.com/en/index.html", PHP_URL_HOST));
                // log::alert(parse_url("https://techpubs.deere.com/", PHP_URL_HOST));
               
                // if(substr($row['url'],0,7) == "http://"){
                //     log::alert("http");
                //     log::alert(substr(substr_replace($row['url'],"https:/",0).substr($row['url'],6),0,-1));
                // }else{
                //     log::alert("https");
                //     // log::alert(substr(substr_replace($row['url'],"https:/",0).substr($row['url'],6),0,-1));
                // }
                // die;
                if (!$CompanyUrlName = CompanyUrlName::where('company_url_name',"https://".parse_url($row['url'], PHP_URL_HOST))->first()) {
                    log::alert('demo-1.0');
                    log::alert($row['company_name']);
                    $LastcompanyId = Company::orderBy('id', 'desc')->first();
                    $company = DB::table('extension_company')->insert([
                                "id" => $LastcompanyId ? $LastcompanyId->id +1 : 1,
                                "company_name" => $row['company_name'],
                                "company_logo" => ''
                                ]);
          
                    // log::alert(Company::orderBy('id', 'desc')->first()->id +1);
                    log::alert('demo-1.1');
                    $lastCompanyUrlName = CompanyUrlName::orderBy('id', 'desc')->first();
                    $CompanyUrlNameCreate = CompanyUrlName::create([
                                                "id" => $lastCompanyUrlName ?  $lastCompanyUrlName->id +1 : 1,
                                                "company_url_name" => "https://".parse_url($row['url'], PHP_URL_HOST),
                                                "company_id" => company::where('company_name', $row['company_name'])->first()->id
                                            ]);
                    log::alert('demo-1.2');
                }
                log::alert('demo-2');
                $import_company = [];
                if (!in_array($row['company_name'], $import_company)) {
                    log::alert('demo-2.1');
                    array_push($import_company, $row['company_name']);
                    $CompanyUrlNameId = CompanyUrlName::where('company_url_name', "https://".parse_url($row['url'], PHP_URL_HOST))->first()->company_id;
                    // $privacyDetail = company::where('company_name', $row['Name'])->first();
                    log::alert('demo-2.2');
                    $date = new DateTime;
                    $endTime = $date->format('Y-m-d H:i:s');
                    $oldPrivacyStatementData = PrivacyStatement::where('company', $CompanyUrlNameId)->first();
                    if ($oldPrivacyStatementData) {
                        $oldPrivacyStatementData['help_privacystatement_id'] = $oldPrivacyStatementData['id'];
                        $oldPrivacyStatementData['created_at'] = $endTime;
                        $oldPrivacyStatementData['updated_at'] = $endTime;
                        log::alert('demo-2.3');
                        // HistoryHelpPrivacystatement::create($oldPrivacyStatementData);
                        HistoryHelpPrivacystatement::create([
                            "help_privacystatement_id" => $oldPrivacyStatementData->id ,
                            "account_balance" => $oldPrivacyStatementData->account_balance,
                            "address" => $oldPrivacyStatementData->address,
                            "advertiser_id" => $oldPrivacyStatementData->advertiser_id,
                            "age" => $oldPrivacyStatementData->age,
                            "agent_number" => $oldPrivacyStatementData->agent_number,
                            "attorneys_name" => $oldPrivacyStatementData->attorneys_name,
                            "biometric_data" => $oldPrivacyStatementData->biometric_data,
                            "birth_certificate" => $oldPrivacyStatementData->birth_certificate,
                            "browsing_history" => $oldPrivacyStatementData->browsing_history,
                            "claim_loss_date" => $oldPrivacyStatementData->claim_loss_date,
                            "claim_loss_type" => $oldPrivacyStatementData->claim_loss_type,
                            "claim_number" => $oldPrivacyStatementData->claim_number,
                            "claim_peril" => $oldPrivacyStatementData->claim_peril,
                            "claim_status" => $oldPrivacyStatementData->claim_status,
                            "consumer_spending_habits" => $oldPrivacyStatementData->consumer_spending_habits,
                            "cookie" => $oldPrivacyStatementData->cookie,
                            "credit_or_debit_card_account_number" => $oldPrivacyStatementData->credit_or_debit_card_account_number,
                            "credit_or_debit_card_expiration_date" => $oldPrivacyStatementData->credit_or_debit_card_expiration_date,
                            "credit_or_debit_card_security_code" => $oldPrivacyStatementData->credit_or_debit_card_security_code,
                            "credit_score" => $oldPrivacyStatementData->credit_score,
                            "criminal_conviction" => $oldPrivacyStatementData->criminal_conviction,
                            "customer_number" => $oldPrivacyStatementData->customer_number,
                            "customer_spending_habits" => $oldPrivacyStatementData->customer_spending_habits,
                            "customers_income" => $oldPrivacyStatementData->customers_income,
                            "date_of_birth" => $oldPrivacyStatementData->date_of_birth,
                            "device_id" => $oldPrivacyStatementData->device_id,
                            "dna_profile" => $oldPrivacyStatementData->dna_profile,
                            "driver_authorization_card_number" => $oldPrivacyStatementData->driver_authorization_card_number,
                            "drivers_license_number" => $oldPrivacyStatementData->drivers_license_number,
                            "dwelling_estimated_home_value" => $oldPrivacyStatementData->dwelling_estimated_home_value,
                            "dwelling_mortgage_amount" => $oldPrivacyStatementData->dwelling_mortgage_amount,
                            "dwelling_property_index_number_pin" => $oldPrivacyStatementData->dwelling_property_index_number_pin,
                            "dwelling_purchase_price_amount" => $oldPrivacyStatementData->dwelling_purchase_price_amount,
                            "dwelling_type" => $oldPrivacyStatementData->dwelling_type,
                            "education" => $oldPrivacyStatementData->education,
                            "emaile_address" => $oldPrivacyStatementData->emaile_address,
                            "employee_identification_number" => $oldPrivacyStatementData->employee_identification_number,
                            "employee_identification_number_includeing_social_security_numbe" => $oldPrivacyStatementData->employee_identification_number_includeing_social_security_numbe,
                            "employee_job_title" => $oldPrivacyStatementData->employee_job_title,
                            "employee_pay_grade" => $oldPrivacyStatementData->employee_pay_grade,
                            "employee_position_id" => $oldPrivacyStatementData->employee_position_id,
                            "employee_salary" => $oldPrivacyStatementData->employee_salary,
                            "employee_service_date" => $oldPrivacyStatementData->employee_service_date,
                            "employee_type" => $oldPrivacyStatementData->employee_type,
                            "employee_tax_identification_number" => $oldPrivacyStatementData->employee_tax_identification_number,
                            "employee_status" => $oldPrivacyStatementData->employee_status,
                            "estimated_household_income_range" => $oldPrivacyStatementData->estimated_household_income_range,
                            "ethnic_origin" => $oldPrivacyStatementData->ethnic_origin,
                            "fax_number" => $oldPrivacyStatementData->fax_number,
                            "financial_account_number" => $oldPrivacyStatementData->financial_account_number,
                            "financial_account_security_code" => $oldPrivacyStatementData->financial_account_security_code,
                            "gender" => $oldPrivacyStatementData->gender,
                            "geo_location" => $oldPrivacyStatementData->geo_location,
                            "health_insurance_claim_number" => $oldPrivacyStatementData->health_insurance_claim_number,
                            "health_insurance_identification_number" => $oldPrivacyStatementData->health_insurance_identification_number,
                            "health_insurance_participant_id" => $oldPrivacyStatementData->health_insurance_participant_id,
                            "health_insurance_plan_id" => $oldPrivacyStatementData->health_insurance_plan_id,
                            "health_insurance_plan_provider" => $oldPrivacyStatementData->health_insurance_plan_provider,
                            "health_insurance_plan_type" => $oldPrivacyStatementData->health_insurance_plan_type,
                            "health_related_payment_history" => $oldPrivacyStatementData->health_related_payment_history,
                            "height" => $oldPrivacyStatementData->height,
                            "inferences" => $oldPrivacyStatementData->inferences,
                            "marital_status" => $oldPrivacyStatementData->marital_status,
                            "marriage_certificate" => $oldPrivacyStatementData->marriage_certificate,
                            "medical_diagnosis_or_treatment" => $oldPrivacyStatementData->medical_diagnosis_or_treatment,
                            "medical_history" => $oldPrivacyStatementData->medical_history,
                            "medical_identification_number" => $oldPrivacyStatementData->medical_identification_number,
                            "medical_record_number" => $oldPrivacyStatementData->medical_record_number,
                            "metal_health" => $oldPrivacyStatementData->metal_health,
                            "military_id_number" => $oldPrivacyStatementData->military_id_number,
                            "mothers_maiden_name" => $oldPrivacyStatementData->mothers_maiden_name,
                            "name" => $oldPrivacyStatementData->name,
                            "names_of_employers" => $oldPrivacyStatementData->names_of_employers,
                            "number_of_dependents" => $oldPrivacyStatementData->number_of_dependents,
                            "occupation" => $oldPrivacyStatementData->occupation,
                            "other_government_issued_identification_number" => $oldPrivacyStatementData->other_government_issued_identification_number,
                            "passport_number" => $oldPrivacyStatementData->passport_number,
                            "personal_internet_protocal_address" => $oldPrivacyStatementData->personal_internet_protocal_address,
                            "personal_web_url" => $oldPrivacyStatementData->personal_web_url,
                            "photographic_or_video_images" => $oldPrivacyStatementData->photographic_or_video_images,
                            "physicians_name" => $oldPrivacyStatementData->physicians_name,
                            "plan_beneficiary_number" => $oldPrivacyStatementData->plan_beneficiary_number,
                            "policy_coverage_and_limit" => $oldPrivacyStatementData->policy_coverage_and_limit,
                            "policy_effective_date" => $oldPrivacyStatementData->policy_effective_date,
                            "policy_expiration_date" => $oldPrivacyStatementData->policy_expiration_date,
                            "policy_number" => $oldPrivacyStatementData->policy_number,
                            "policy_status" => $oldPrivacyStatementData->policy_status,
                            "policy_type" => $oldPrivacyStatementData->policy_type,
                            "political_party_affiliation" => $oldPrivacyStatementData->political_party_affiliation,
                            "prior_insurance_information" => $oldPrivacyStatementData->prior_insurance_information,
                            "professional_certificate_License_number" => $oldPrivacyStatementData->professional_certificate_License_number,
                            "race" => $oldPrivacyStatementData->race,
                            "religious_affiliation" => $oldPrivacyStatementData->religious_affiliation,
                            "search_history" => $oldPrivacyStatementData->search_history,
                            "sexual_behavPhysicians_nameior_lifestyle" => $oldPrivacyStatementData->sexual_behavPhysicians_nameior_lifestyle,
                            "signature_digital_or_electronic" => $oldPrivacyStatementData->signature_digital_or_electronic,
                            "signature_written" => $oldPrivacyStatementData->signature_written,
                            "smoker_status" => $oldPrivacyStatementData->smoker_status,
                            "social_security_number" => $oldPrivacyStatementData->social_security_number,
                            "state_identification_card_number" => $oldPrivacyStatementData->state_identification_card_number,
                            "student_identification_number" => $oldPrivacyStatementData->student_identification_number,
                            "taxpayer_identification_number" => $oldPrivacyStatementData->taxpayer_identification_number,
                            "telephone_number" => $oldPrivacyStatementData->telephone_number,
                            "thermal_or_olfactory_information" => $oldPrivacyStatementData->thermal_or_olfactory_information,
                            "trade_union_membership" => $oldPrivacyStatementData->trade_union_membership,
                            "tribal_identification_number" => $oldPrivacyStatementData->tribal_identification_number,
                            "user_alias" => $oldPrivacyStatementData->user_alias,
                            "user_name_id" => $oldPrivacyStatementData->user_name_id,
                            "user_name_id_and_password" => $oldPrivacyStatementData->user_name_id_and_password,
                            "vehicle_identification_number" => $oldPrivacyStatementData->vehicle_identification_number,
                            "vehicle_license_number" => $oldPrivacyStatementData->vehicle_license_number,
                            "web_beacon" => $oldPrivacyStatementData->web_beacon,
                            "weight" => $oldPrivacyStatementData->weight,
                            "weird_things" => $oldPrivacyStatementData->weird_things,
                            "how_enterprises_collect_your_information" => $oldPrivacyStatementData->how_enterprises_collect_your_information,
                            "when_enterprises_collects_your_information" => $oldPrivacyStatementData->when_enterprises_collects_your_information,
                            "ways_enterprise_uses_your_information" => $oldPrivacyStatementData->ways_enterprise_uses_your_information,
                            "who_else_sees_and_uses_your_information" => $oldPrivacyStatementData->who_else_sees_and_uses_your_information,
                            "california_consumer_request_information_link" => $oldPrivacyStatementData->california_consumer_request_information_link,
                            "alifornia_consumer_request_information_phone_number" => $oldPrivacyStatementData->alifornia_consumer_request_information_phone_number,
                            "california_consumer_request_information_other" => $oldPrivacyStatementData->california_consumer_request_information_other,
                            "for_Non_CA_citizen_action_rights_other" => $oldPrivacyStatementData->for_Non_CA_citizen_action_rights_other,
                            "for_Non_CA_citizen_action_rights_phone_number" => $oldPrivacyStatementData->for_Non_CA_citizen_action_rights_phone_number,
                            "for_Non_CA_citizen_action_rights_link" => $oldPrivacyStatementData->for_Non_CA_citizen_action_rights_link,
                            "in_compliance_with_CCPA_existance" => $oldPrivacyStatementData->in_compliance_with_CCPA_existance,
                            "in_compliance_with_CCPA_statement" => $oldPrivacyStatementData->in_compliance_with_CCPA_statement,
                            "in_compliance_for_GDPR_existance" => $oldPrivacyStatementData->in_compliance_for_GDPR_existance,
                            "in_compliance_for_GDPR_statement" => $oldPrivacyStatementData->in_compliance_for_GDPR_statement,
                            "elroi_internal_date" => $oldPrivacyStatementData->elroi_internal_date,
                            "qa_1st" => $oldPrivacyStatementData->qa_1st,
                            "qa_2nd" => $oldPrivacyStatementData->qa_2nd,
                            "company" => $oldPrivacyStatementData->company,
                            "elroi_internal_author" => $oldPrivacyStatementData->elroi_internal_author,
                        ]);
                        log::alert('demo-2.4');
                    }
                    $array1= preg_split("/\,/", $row['ca_weird_things_they_collect_about_you_information_collected_please_check_all_explanation_personal_information_data_elements_collected_by_the_enterprise_which_includes_the_type_of_data_collected_if_the_option_is_unavailable_check_other_and_comment_the_data_element']);
                    // log::alert($array1);
          
                    $array2= preg_split("/\,/", $row['information_collected_please_check_all_explanation_personal_information_data_elements_collected_by_the_enterprise_which_includes_the_type_of_data_collected_if_the_option_is_unavailable_check_other_and_comment_the_data_element_weird_things_they_collect_about_you']);
                 
                    log::alert('demo-2.5');
                    $excelFeilds = str_replace(' ', '_', array_map('trim', array_map('strtolower', array_unique(array_merge($array1, $array2), SORT_REGULAR))));
         
                    if ($oldPrivacyStatementData) {
                        $PrivacyStatementData = PrivacyStatement::where('company', $CompanyUrlNameId)->first();
                    } else {
                        $lastprivacydata = PrivacyStatement::orderBy('id', 'desc')->first();
                        $PrivacyStatementData = new PrivacyStatement();
                        $PrivacyStatementData->id = $lastprivacydata ? $lastprivacydata->id +1 : 1;
                    }
                    log::alert('demo-2.6');
                    $PrivacyStatementData->account_balance= array_search('account_balance', $excelFeilds) ? true : false;
                    $PrivacyStatementData->address= array_search('address', $excelFeilds) ? true : false;
                    $PrivacyStatementData->advertiser_id= array_search('advertiser_id', $excelFeilds) ? true : false;
                    $PrivacyStatementData->age= array_search('age', $excelFeilds) ? true : false;
                    $PrivacyStatementData->agent_number= array_search('agent_number', $excelFeilds) ? true : false;
                    $PrivacyStatementData->attorneys_name= array_search('attorneys_name', $excelFeilds) ? true : false;
                    $PrivacyStatementData->biometric_data= array_search('biometric_data', $excelFeilds) ? true : false;
                    $PrivacyStatementData->birth_certificate= array_search('birth_certificate', $excelFeilds) ? true : false;
                    $PrivacyStatementData->browsing_history= array_search('browsing_history', $excelFeilds) ? true : false;
                    $PrivacyStatementData->claim_loss_date= array_search('claim_loss_date', $excelFeilds) ? true : false;
                    $PrivacyStatementData->claim_loss_type= array_search('claim_loss_type', $excelFeilds) ? true : false;
                    $PrivacyStatementData->claim_number= array_search('claim_number', $excelFeilds) ? true : false;
                    $PrivacyStatementData->claim_peril= array_search('claim_peril', $excelFeilds) ? true : false;
                    $PrivacyStatementData->claim_status= array_search('claim_status', $excelFeilds) ? true : false;
                    $PrivacyStatementData->consumer_spending_habits= array_search('consumer_spending_habits', $excelFeilds) ? true : false;
                    $PrivacyStatementData->cookie= array_search('cookie', $excelFeilds) ? true : false;
                    $PrivacyStatementData->credit_or_debit_card_account_number= array_search('credit_or_debit_card_account_number', $excelFeilds) ? true : false;
                    $PrivacyStatementData->credit_or_debit_card_expiration_date= array_search('credit_or_debit_card_expiration_date', $excelFeilds) ? true : false;
                    $PrivacyStatementData->credit_or_debit_card_security_code= array_search('credit_or_debit_card_security_code', $excelFeilds) ? true : false;
                    $PrivacyStatementData->credit_score= array_search('credit_score', $excelFeilds) ? true : false;
                    $PrivacyStatementData->criminal_conviction= array_search('criminal_conviction', $excelFeilds) ? true : false;
                    $PrivacyStatementData->customer_number= array_search('customer_number', $excelFeilds) ? true : false;
                    $PrivacyStatementData->customer_spending_habits= array_search('customer_spending_habits', $excelFeilds) ? true : false;
                    $PrivacyStatementData->customers_income= array_search('customers_income', $excelFeilds) ? true : false;
                    $PrivacyStatementData->date_of_birth= array_search('date_of_birth', $excelFeilds) ? true : false;
                    $PrivacyStatementData->device_id= array_search('device_id', $excelFeilds) ? true : false;
                    $PrivacyStatementData->dna_profile= array_search('dna_profile', $excelFeilds) ? true : false;
                    $PrivacyStatementData->driver_authorization_card_number= array_search('driver_authorization_card_number', $excelFeilds) ? true : false;
                    $PrivacyStatementData->drivers_license_number= array_search('drivers_license_number', $excelFeilds) ? true : false;
                    $PrivacyStatementData->dwelling_estimated_home_value= array_search('dwelling_estimated_home_value', $excelFeilds) ? true : false;
                    $PrivacyStatementData->dwelling_mortgage_amount= array_search('dwelling_mortgage_amount', $excelFeilds) ? true : false;
                    $PrivacyStatementData->dwelling_property_index_number_pin= array_search('dwelling_property_index_number_pin', $excelFeilds) ? true : false;
                    $PrivacyStatementData->dwelling_purchase_price_amount= array_search('dwelling_purchase_price_amount', $excelFeilds) ? true : false;
                    $PrivacyStatementData->dwelling_type= array_search('dwelling_type', $excelFeilds) ? true : false;
                    $PrivacyStatementData->education= array_search('education', $excelFeilds) ? true : false;
                    $PrivacyStatementData->emaile_address= array_search('emaile_address', $excelFeilds) ? true : false;
                    $PrivacyStatementData->employee_identification_number= array_search('employee_identification_number', $excelFeilds) ? true : false;
                    $PrivacyStatementData->employee_identification_number_includeing_social_security_numbe= array_search('employee_identification_number_includeing_social_security_numbe', $excelFeilds) ? true : false;
                    $PrivacyStatementData->employee_job_title= array_search('employee_job_title', $excelFeilds) ? true : false;
                    $PrivacyStatementData->employee_pay_grade= array_search('employee_pay_grade', $excelFeilds) ? true : false;
                    $PrivacyStatementData->employee_position_id= array_search('employee_position_id', $excelFeilds) ? true : false;
                    $PrivacyStatementData->employee_salary= array_search('employee_salary', $excelFeilds) ? true : false;
                    $PrivacyStatementData->employee_service_date= array_search('employee_service_date', $excelFeilds) ? true : false;
                    $PrivacyStatementData->employee_type= array_search('employee_type', $excelFeilds) ? true : false;
                    $PrivacyStatementData->employee_tax_identification_number= array_search('employee_tax_identification_number', $excelFeilds) ? true : false;
                    $PrivacyStatementData->employee_status= array_search('employee_status', $excelFeilds) ? true : false;
                    $PrivacyStatementData->estimated_household_income_range= array_search('estimated_household_income_range', $excelFeilds) ? true : false;
                    $PrivacyStatementData->ethnic_origin= array_search('ethnic_origin', $excelFeilds) ? true : false;
                    $PrivacyStatementData->fax_number= array_search('fax_number', $excelFeilds) ? true : false;
                    $PrivacyStatementData->financial_account_number= array_search('financial_account_number', $excelFeilds) ? true : false;
                    $PrivacyStatementData->financial_account_security_code= array_search('financial_account_security_code', $excelFeilds) ? true : false;
                    $PrivacyStatementData->gender= array_search('gender', $excelFeilds) ? true : false;
                    $PrivacyStatementData->geo_location= array_search('geo_location', $excelFeilds) ? true : false;
                    $PrivacyStatementData->health_insurance_claim_number= array_search('health_insurance_claim_number', $excelFeilds) ? true : false;
                    $PrivacyStatementData->health_insurance_identification_number= array_search('health_insurance_identification_number', $excelFeilds) ? true : false;
                    $PrivacyStatementData->health_insurance_participant_id= array_search('health_insurance_participant_id', $excelFeilds) ? true : false;
                    $PrivacyStatementData->health_insurance_plan_id= array_search('health_insurance_plan_id', $excelFeilds) ? true : false;
                    $PrivacyStatementData->health_insurance_plan_provider= array_search('health_insurance_plan_provider', $excelFeilds) ? true : false;
                    $PrivacyStatementData->health_insurance_plan_type= array_search('health_insurance_plan_type', $excelFeilds) ? true : false;
                    $PrivacyStatementData->health_related_payment_history= array_search('health_related_payment_history', $excelFeilds) ? true : false;
                    $PrivacyStatementData->height= array_search('height', $excelFeilds) ? true : false;
                    $PrivacyStatementData->inferences= array_search('inferences', $excelFeilds) ? true : false;
                    $PrivacyStatementData->marital_status= array_search('marital_status', $excelFeilds) ? true : false;
                    $PrivacyStatementData->marriage_certificate= array_search('marriage_certificate', $excelFeilds) ? true : false;
                    $PrivacyStatementData->medical_diagnosis_or_treatment= array_search('medical_diagnosis_or_treatment', $excelFeilds) ? true : false;
                    $PrivacyStatementData->medical_history= array_search('medical_history', $excelFeilds) ? true : false;
                    $PrivacyStatementData->medical_identification_number= array_search('medical_identification_number', $excelFeilds) ? true : false;
                    $PrivacyStatementData->medical_record_number= array_search('medical_record_number', $excelFeilds) ? true : false;
                    $PrivacyStatementData->metal_health= array_search('metal_health', $excelFeilds) ? true : false;
                    $PrivacyStatementData->military_id_number= array_search('military_id_number', $excelFeilds) ? true : false;
                    $PrivacyStatementData->mothers_maiden_name= array_search('mothers_maiden_name', $excelFeilds) ? true : false;
                    $PrivacyStatementData->name= array_search('name', $excelFeilds) ? true : false;
                    $PrivacyStatementData->names_of_employers= array_search('names_of_employers', $excelFeilds) ? true : false;
                    $PrivacyStatementData->names_of_relatives= array_search('names_of_relatives', $excelFeilds) ? true : false;
                    $PrivacyStatementData->number_of_dependents= array_search('number_of_dependents', $excelFeilds) ? true : false;
                    $PrivacyStatementData->occupation= array_search('occupation', $excelFeilds) ? true : false;
                    $PrivacyStatementData->other_government_issued_identification_number= array_search('other_government_issued_identification_number', $excelFeilds) ? true : false;
                    $PrivacyStatementData->passport_number= array_search('passport_number', $excelFeilds) ? true : false;
                    $PrivacyStatementData->personal_internet_protocal_address= array_search('personal_internet_protocal_address', $excelFeilds) ? true : false;
                    $PrivacyStatementData->personal_web_url= array_search('personal_web_url', $excelFeilds) ? true : false;
                    $PrivacyStatementData->photographic_or_video_images= array_search('photographic_or_video_images', $excelFeilds) ? true : false;
                    $PrivacyStatementData->physicians_name= array_search('physicians_name', $excelFeilds) ? true : false;
                    $PrivacyStatementData->plan_beneficiary_number= array_search('plan_beneficiary_number', $excelFeilds) ? true : false;
                    $PrivacyStatementData->policy_coverage_and_limit= array_search('policy_coverage_and_limit', $excelFeilds) ? true : false;
                    $PrivacyStatementData->policy_effective_date= array_search('policy_effective_date', $excelFeilds) ? true : false;
                    $PrivacyStatementData->policy_expiration_date= array_search('policy_expiration_date', $excelFeilds) ? true : false;
                    $PrivacyStatementData->policy_number= array_search('policy_number', $excelFeilds) ? true : false;
                    $PrivacyStatementData->policy_status= array_search('policy_status', $excelFeilds) ? true : false;
                    $PrivacyStatementData->policy_type= array_search('policy_type', $excelFeilds) ? true : false;
                    $PrivacyStatementData->political_party_affiliation= array_search('political_party_affiliation', $excelFeilds) ? true : false;
                    $PrivacyStatementData->prior_insurance_information= array_search('prior_insurance_information', $excelFeilds) ? true : false;
                    $PrivacyStatementData->professional_certificate_License_number= array_search('professional_certificate_License_number', $excelFeilds) ? true : false;
                    $PrivacyStatementData->race= array_search('race', $excelFeilds) ? true : false;
                    $PrivacyStatementData->religious_affiliation= array_search('religious_affiliation', $excelFeilds) ? true : false;
                    $PrivacyStatementData->search_history= array_search('search_history', $excelFeilds) ? true : false;
                    $PrivacyStatementData->sexual_behavPhysicians_nameior_lifestyle= array_search('sexual_behavPhysicians_nameior_lifestyle', $excelFeilds) ? true : false;
                    $PrivacyStatementData->signature_digital_or_electronic= array_search('signature_digital_or_electronic', $excelFeilds) ? true : false;
                    $PrivacyStatementData->signature_written= array_search('signature_written', $excelFeilds) ? true : false;
                    $PrivacyStatementData->smoker_status= array_search('smoker_status', $excelFeilds) ? true : false;
                    $PrivacyStatementData->social_security_number= array_search('social_security_number', $excelFeilds) ? true : false;
                    $PrivacyStatementData->state_identification_card_number= array_search('state_identification_card_number', $excelFeilds) ? true : false;
                    $PrivacyStatementData->student_identification_number= array_search('student_identification_number', $excelFeilds) ? true : false;
                    $PrivacyStatementData->taxpayer_identification_number= array_search('taxpayer_identification_number', $excelFeilds) ? true : false;
                    $PrivacyStatementData->telephone_number= array_search('telephone_number', $excelFeilds) ? true : false;
                    $PrivacyStatementData->thermal_or_olfactory_information= array_search('thermal_or_olfactory_information', $excelFeilds) ? true : false;
                    $PrivacyStatementData->trade_union_membership= array_search('trade_union_membership', $excelFeilds) ? true : false;
                    $PrivacyStatementData->tribal_identification_number= array_search('tribal_identification_number', $excelFeilds) ? true : false;
                    $PrivacyStatementData->user_alias= array_search('user_alias', $excelFeilds) ? true : false;
                    $PrivacyStatementData->user_name_id= array_search('user_name_id', $excelFeilds) ? true : false;
                    $PrivacyStatementData->user_name_id_and_password= array_search('user_name_id_and_password', $excelFeilds) ? true : false;
                    $PrivacyStatementData->vehicle_identification_number= array_search('vehicle_identification_number', $excelFeilds) ? true : false;
                    $PrivacyStatementData->vehicle_license_number= array_search('vehicle_license_number', $excelFeilds) ? true : false;
                    $PrivacyStatementData->web_beacon= array_search('web_beacon', $excelFeilds) ? true : false;
                    $PrivacyStatementData->weight= array_search('weight', $excelFeilds) ? true : false;
                    $PrivacyStatementData->weird_things= isset($row['weird_things_they_collect_about_you_please_list_all_data_elements_collected_that_were_not_listed_above_if_the_privacy_policy_did_not_specifically_enumerate_the_data_elements_collected_please_copy_the_general_information_and_place_it_below']) ? $row['weird_things_they_collect_about_you_please_list_all_data_elements_collected_that_were_not_listed_above_if_the_privacy_policy_did_not_specifically_enumerate_the_data_elements_collected_please_copy_the_general_information_and_place_it_below'] : null;
                    $PrivacyStatementData->how_enterprises_collect_your_information= isset($row['information_collected_please_provide_an_explanation_of_how_the_personal_information_data_elements_are_collected_by_the_enterrpise_how_do_they_get_your_information']) ? $row['information_collected_please_provide_an_explanation_of_how_the_personal_information_data_elements_are_collected_by_the_enterrpise_how_do_they_get_your_information'] : null;
                    $PrivacyStatementData->when_enterprises_collects_your_information= isset($row['information_collected_please_provide_an_explanation_of_when_the_personal_information_data_elements_are_collected_by_the_enterprise_when_is_your_information_collected']) ? $row['information_collected_please_provide_an_explanation_of_when_the_personal_information_data_elements_are_collected_by_the_enterprise_when_is_your_information_collected'] : null;
            
                    $PrivacyStatementData->ways_enterprise_uses_your_information= isset($row['maintenance_and_security_please_provide_an_explanation_of_the_enterprises_mechanisms_for_data_maintenance_and_security_ways_they_protect_your_information']) ? $row['maintenance_and_security_please_provide_an_explanation_of_the_enterprises_mechanisms_for_data_maintenance_and_security_ways_they_protect_your_information'] : null;

                    $PrivacyStatementData->who_else_sees_and_uses_your_information= isset($row['processing_please_provide_an_explanation_of_the_enterprises_policy_with_regard_to_sharing_data_with_third_party_affiliates_what_are_the_consumer_consent_and_notice_mechanisms_offered_by_the_enterprise_who_sees_your_information']) ? $row['processing_please_provide_an_explanation_of_the_enterprises_policy_with_regard_to_sharing_data_with_third_party_affiliates_what_are_the_consumer_consent_and_notice_mechanisms_offered_by_the_enterprise_who_sees_your_information'] : null;
                    $PrivacyStatementData->california_consumer_request_information_link= isset($row['ca_please_provide_the_customer_request_portal_information_if_there_is_a_link_and_phone_number_please_provide_both']) ? $row['ca_please_provide_the_customer_request_portal_information_if_there_is_a_link_and_phone_number_please_provide_both'] : null;
                    $PrivacyStatementData->alifornia_consumer_request_information_phone_number= isset($row['ca_please_provide_the_customer_request_portal_information_if_there_is_a_link_and_phone_number_please_provide_both']) ? $row['ca_please_provide_the_customer_request_portal_information_if_there_is_a_link_and_phone_number_please_provide_both'] : null;
                    $PrivacyStatementData->california_consumer_request_information_other= isset($row['ca_please_provide_the_customer_request_portal_information_if_there_is_a_link_and_phone_number_please_provide_both']) ? $row['ca_please_provide_the_customer_request_portal_information_if_there_is_a_link_and_phone_number_please_provide_both'] : null;
                    $PrivacyStatementData->for_Non_CA_citizen_action_rights_other= isset($row['please_provide_the_customer_request_portal_information_if_there_is_a_link_and_phone_number_please_provide_both']) ? $row['please_provide_the_customer_request_portal_information_if_there_is_a_link_and_phone_number_please_provide_both'] : null;
                    $PrivacyStatementData->for_Non_CA_citizen_action_rights_phone_number= isset($row['please_provide_the_customer_request_portal_information_if_there_is_a_link_and_phone_number_please_provide_both']) ? $row['please_provide_the_customer_request_portal_information_if_there_is_a_link_and_phone_number_please_provide_both'] : null;
                    $PrivacyStatementData->for_Non_CA_citizen_action_rights_link= isset($row['please_provide_the_customer_request_portal_information_if_there_is_a_link_and_phone_number_please_provide_both']) ? $row['please_provide_the_customer_request_portal_information_if_there_is_a_link_and_phone_number_please_provide_both'] : null;
                    $PrivacyStatementData->in_compliance_with_CCPA_existance= isset($row['is_this_policy_ccpa_compliant']) ? $row['is_this_policy_ccpa_compliant'] : null;
                    $PrivacyStatementData->in_compliance_with_CCPA_statement= isset($row['if_this_policy_is_not_ccpa_compliant_please_explain_why_using_the_crac_method']) ? $row['if_this_policy_is_not_ccpa_compliant_please_explain_why_using_the_crac_method'] : null;

                    $PrivacyStatementData->in_compliance_for_GDPR_existance= isset($row['is_this_policy_gdpr_compliant']) ? $row['is_this_policy_gdpr_compliant'] : null;
           
                    $PrivacyStatementData->in_compliance_for_GDPR_statement= isset($row['if_this_policy_is_not_gdpr_compliant_please_explain_why_using_the_crac_method']) ? $row['if_this_policy_is_not_gdpr_compliant_please_explain_why_using_the_crac_method'] : null;
                    log::alert('date');
                    log::alert(gettype($row['timestamp']));
                    if ($row['timestamp'] && gettype($row['timestamp']) !== 'string') {
                        $elroi_internal_date = $this->transformDate($row['timestamp']);
                    } else {
                        $elroi_internal_date = Carbon::now() ;
                    }
                   
                  
                    log::alert($elroi_internal_date);
                    log::alert('date-2');
                    // log::alert($this->transformDate($row['timestamp']));
                    $PrivacyStatementData->elroi_internal_date=  $elroi_internal_date ;
                    // $PrivacyStatementData->qa_1st= array_search('qa_1st',$excelFeilds) ? true : false;
                    // $PrivacyStatementData->qa_2nd= array_search('qa_2nd',$excelFeilds) ? true : false;
                    $PrivacyStatementData->company= $CompanyUrlNameId;
                    // $PrivacyStatementData->elroi_internal_author = array_search('elroi_internal_author ',$excelFeilds) ? true : false;
                    $PrivacyStatementData->save();
                    log::alert('demo-2.7');
                    log::alert("key-".$key);
                } else {
                }
            }
        }
    }
    public function transformDate($value, $format = 'Y-m-d')
    {
        try {
            return \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
        } catch (\ErrorException $e) {
            return \Carbon\Carbon::createFromFormat($format, $value);
        }
    }
}
