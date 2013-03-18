FuelSDK-Ruby
============

ExactTarget Fuel SDK for PHP

## Overview ##
The Fuel SDK for PHP provides easy access to ExactTarget's Fuel API Family services, including a collection of REST APIs and a SOAP API. These APIs provide access to ExactTarget functionality via common collection types such as array/hash. 

## Requirements ##
PHP Version 5.2.3

Extensions:

- mcrypt
- openssl
- SOAP


## Getting Started ##
After downloading the project, rename the config.php.template file to config.php. 

Edit config.php so you can input the ClientID and Client Secret values provided when you registered your application. If you are building a HubExchange application for the Interactive Marketing Hub then, you must also provide the Application Signature (appsignature).  Only change the value for the defaultwsdl configuration item if instructed by ExactTarget.

If you have not registered your application or you need to lookup your Application Key or Application Signature values, please go to App Center at [Code@: ExactTarget's Developer Community](http://code.exacttarget.com/appcenter "Code@ App Center").

## Example Request ##
All ExactTarget objects exposed through the Fuel SDK begin with be prefixed with "ET\_".  Start by working with the ET_List object:

Add a require statement to reference the Fuel SDK's functionality:
> require('ET_Client.php');

Next, create an instance of the ET_Client class:
> $myclient = new ET_Client();

Create an instance of the object type we want to work with:
> $getList = new ET_List();

Associate the ET_Client to the object using the authStub property:
> $getList->authStub = $myclient;

Utilize one of the ET_List methods:
> $getResponse = $getList->get();	

Print out the results for viewing
> print_r($getResponse);

**Example Output:**

<pre>
ET_Get Object
(
    [status] => 1
    [code] => 200
    [message] =>
    [results] => Array
        (
            [0] => stdClass Object
                (
                    [Client] => stdClass Object
                        (
                            [ID] => 1000001
                            [PartnerClientKey] => 
                        )

                    [PartnerKey] =>
                    [CreatedDate] => 2009-06-12T14:42:06.1
                    [ModifiedDate] => 2011-08-17T14:50:30.697
                    [ID] => 1718921
                    [ObjectID] => f41c7d1b-8957-de11-92ee-001cc494ae9e
                    [CustomerKey] => All Subscribers - 578623
                    [ListName] => All Subscribers
                    [Category] => 578623
                    [Type] => Private
                    [Description] => Contains all subscribers
                    [ListClassification] => ExactTargetList
                )

        )

    [request_id] => 5d56a37e-4b13-4f0a-aa13-2e108e60a990
    [moreResults] => 
)
</pre>

## ET\_Client Class ##

The ET\_Client class takes care of many of the required steps when accessing ExactTarget's API, including retrieving appropriate access tokens, handling token state for managing refresh, and determining the appropriate endpoints for API requests.  In order to leverage the advantages this class provides, use a single instance of this class for an entire session.  Do not instantiate a new ET_Client object for each request made. 

## Responses ##
All methods on Fuel SDK objects return a generic object that follows the same structure, regardless of the type of call.  This object contains a common set of properties used to display details about the request.

- status: Boolean value that indicates if the call was successful
- code: HTTP Error Code (will always be 200 for SOAP requests)
- message: Text values containing more details in the event of an error
- results: Collection containing the details unique to the method called. 

Get Methods also return an addition value to indicate if more information is available (that information can be retrieved using the getMoreResults method):

 - moreResults - Boolean value that indicates on Get requests if more data is available. 


## Samples ##
Find more sample files that illustrate using all of the available functions for ExactTarget objects exposed through the API in the objsamples directory. 

Sample List:

 - [BounceEvent](https://github.com/ExactTarget/FuelSDK-PHP/blob/master/objsamples/sample-bounceevent.php)
 - [Campaign](https://github.com/ExactTarget/FuelSDK-PHP/blob/master/objsamples/sample-campaign.php)
 - [ClickEvent](https://github.com/ExactTarget/FuelSDK-PHP/blob/master/objsamples/sample-clickevent.php)
 - [ContentArea](https://github.com/ExactTarget/FuelSDK-PHP/blob/master/objsamples/sample-contentarea.php)
 - [DataExtension](https://github.com/ExactTarget/FuelSDK-PHP/blob/master/objsamples/sample-dataextension.php)
 - [Email](https://github.com/ExactTarget/FuelSDK-PHP/blob/master/objsamples/sample-email.php)
 - [List](https://github.com/ExactTarget/FuelSDK-PHP/blob/master/objsamples/sample-list.php)
 - [List > Subscriber](https://github.com/ExactTarget/FuelSDK-PHP/blob/master/objsamples/sample-list.subscriber.php)
 - [OpenEvent](https://github.com/ExactTarget/FuelSDK-PHP/blob/master/objsamples/sample-openevent.php)
 - [SentEvent](https://github.com/ExactTarget/FuelSDK-PHP/blob/master/objsamples/sample-sentevent.php)
 - [Subscriber](https://github.com/ExactTarget/FuelSDK-PHP/blob/master/objsamples/sample-subscriber.php)
 - [TriggeredSend](https://github.com/ExactTarget/FuelSDK-PHP/blob/master/objsamples/sample-triggeredsend.php)
 - [UnsubEvent](https://github.com/ExactTarget/FuelSDK-PHP/blob/master/objsamples/sample-unsubevent.php)



 



