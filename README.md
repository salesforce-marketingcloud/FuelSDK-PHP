FuelSDK-PHP
============

ExactTarget Fuel SDK for PHP

## Overview ##
The Fuel SDK for PHP provides easy access to ExactTarget's Fuel API Family services, including a collection of REST APIs and a SOAP API. These APIs provide access to ExactTarget functionality via common collection types such as array/hash. 

## New Features in Version .9 ##
- **Streamlined Folder Support**: All objects that support folders within the UI now have a standardized property called folderId.
- **Interaction Support**: Now supports Import and Email_SendDefinition objects .
- **Profile Attribute Support**: Added the ability to manage profile attributes through the ProfileAttribute object.
- **Support for single request to Add/Update**:A single request can be made which will create the object if one doesn't already or update one if it does.  This works for Subscriber, DataExtension_Row, and List objects using the Put method.
- **Tracking Events Batching Support**: By default, all tracking event types will only pull new data since the last time a request was made using the same filter.  If you would like to override this functionality to pull all data, simply set the GetSinceLastBatch property to false.
- **Automatic Asset Organization for Hub Apps**: Applications that authenticate by providing a JWT will automatically have all created assets placed into a folder based on the HubExchange app's name. 
- **Greater Flexibility for Authentication **: Previously the application keys required for authentication had to be hard-coded in a php config file. While this option is still available, an additional option to pass these at the time the ET_Client class is instantiated allows has been added.  
- **Easier Troubleshooting**: The ability to log the full payload for API requests that are happening behind the scenes has been added in the SDK in order to make troubleshooting issues easier. 

## Requirements ##
PHP Version 5.2.3

Extensions:

- mcrypt
- openssl
- SOAP


## Getting Started ##
After downloading the project, rename the config.php.template file to config.php. 

Edit config.php so you can input the ClientID and ClientSecret values provided when you registered your application. If you are building a HubExchange application for the Interactive Marketing Hub then, you must also provide the Application Signature (appsignature).  Only change the value for the defaultwsdl configuration item if instructed by ExactTarget.

See the ET_Client section below for details on how to specify these values at the time the ET_Client object is instantiated if you would prefer to store the ClientID and ClientSecret values in a database or other configuration storage mechanism. 

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

The ET_Client class accepts multiple parameters

**Refresh WSDL** - If set to true, it will automatically download a local copy of the WSDL whenever an update is found.
> $myclient = new ET_Client(true);

**Debug** - If set to true, all API requests that the Fuel SDK is making behind the scenes will be logged to PHP's error log.  This option should only be set to true in order to troubleshoot during the development process and should never be used in a production scenario. 
> $myclient = new ET_Client(true,true);

**Parameters** - Allows for passing authentication information for use with SSO with a JWT or for passing ClientID/ClientSecret if you would prefer to not use the config file option. 

Example passing JWT: 
> $myclient = new ET_Client(true, array("jwt"=>"JWT Values goes here"));

Example passing ClientID/ClientSecret: 
> $myclient = new ET_Client(true, array("clientid" => "3bjbc3mg4nbk64z5kzczf89n", "clientsecret"=>"ssnGAPvZg6kmm775KPj2Q4Cs"));

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

## Copyright and license ##
Copyright (c) 2013 ExactTarget

Licensed under the MIT License (the "License"); you may not use this work except in compliance with the License. You may obtain a copy of the License in the COPYING file.

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
