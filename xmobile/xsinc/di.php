<?php
/*
$mycomp=new COM("SAPbobsCOM.company") or die ("No connection");
$mycomp->Disconnect();
$mycomp->Server="192.168.50.72:30015";
$mycomp->LicenseServer = "192.168.50.72:30000";
$mycomp->SLDServer = "192.168.50.72:40000";
$mycomp->DbServerType = 9; //9 para dst_HANADB 
$mycomp->UseTrusted = false; 
$mycomp->DbUserName = "SYSTEM";
$mycomp->DbPassword = "Exxis2019";

$mycomp->CompanyDB = "SBO_PRU";
$mycomp->username = "manager2";
$mycomp->password = "1234";


$lRetCode = $mycomp->Connect();

print_r($lRetCode);die;
$vItem = $mycomp->GetBusinessObject(4);
//print_r($vItem);
$RetVal = $vItem->GetByKey("SERVMAN");

echo $mycomp->CompanyName;
echo '<br>';
echo $vItem->Itemname;
echo '<br>';
echo $vItem->CreateDate;

$mycomp->Disconnect();
*/



$mycomp=new COM("SAPbobsCOM.Company") or die ("No connection");
/*try {
    $companyServ=new COM("SAPbobsCOM.UserObjectsMD"); //or die ("No connection");
} catch (\Throwable $th) {
    throw $th;
}*/

/*

$mycomp->Disconnect();
$mycomp->Server="192.168.50.72:30015";
$mycomp->LicenseServer = "192.168.50.72:30000";
$mycomp->SLDServer = "192.168.50.72:40000";
$mycomp->DbServerType = 9; //9 para dst_HANADB 
$mycomp->UseTrusted = false; 
$mycomp->DbUserName = "SYSTEM";
$mycomp->DbPassword = "Exxis2019";

$mycomp->CompanyDB = "SBO_PRU";
$mycomp->username = "manager";
$mycomp->password = "1234";


$lRetCode = $mycomp->Connect();


$oCompanyService = $mycomp->GetCompanyService();
$oGeneralService = $oCompanyService->GetGeneralService("EXXASIT");
$oGeneralData = $oGeneralService->GetDataInterface("SAPbobsCOM.GeneralServiceDataInterfaces.gsGeneralData");//error en esta línea

//$oGeneralData->SetProperty("U_Taller", 5);
var_dump($oGeneralService);die;
//$oCompanyService = $mycomp->GetCompanyService();
//var_dump($oCompanyService);die;
//$oGeneralService = $oCompanyService->GetGeneralService("EXXASIT");
//die('test');
//print_r($lRetCode);
$vItem = $mycomp->GetBusinessObject("EXXASIT");
print_r($vItem);
$RetVal = $vItem->GetByKey(14);

$mycomp->Disconnect();
*/

function connectHana(){
    $server = "192.168.50.72";
    $puerto = "50000";
    $params = ["UserName" => "manager",
                "Password" => "1234",
                "Sociedad" => "SBO_TRAVEX_PROD21"];
    $nombreServ = "Login";
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $server . ":" . $puerto . "/b1s/v1/".$nombreServ);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    //curl_setopt($curl, CURLOPT_VERBOSE, 1);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
    $response = curl_exec($curl)
}
?>