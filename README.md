# Azure Services PHP implementation  

This project implements, using PHP, the Azure Services. Those are used in a simple Symfony web application.  

**Note** An Azure account must be created and the following resources must be configured:

- Azure AD
- Azure Service Bus
- Azure Blob Storage

### Features

- Azure Active Directory
   - This project has a module to authenticate a security principal using Azure AD and Barear Tokens.
    
 
- Azure Service Bus
   - This project has a module to communicate with Azure Service Bus to send messages to a topic.
   - This project has a module to communicate with Azure Service Bus to count the number of queue messages.
   

- Azure Blob Storage
   - This project has a module to communicate with Azure Service Bus to send messages to a topic.
   - This project has a module to communicate with Azure Service Bus to count the number of queue messages.
   

## Installation

1. `composer install`
2. `npm install`
3. `gulp run`
4. `docker-compose up --build -d`
5. Finally open the following ULR in a browser: http://127.0.0.1:8082/index.php  

