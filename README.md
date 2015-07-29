MOLPay CS-Cart Plugin
==================

MOLPay Plugin for CS-Cart Shopping Cart develop by MOLPay technical team.


Notes
-----

MOLPay Sdn. Bhd. is not responsible for any problems that might arise from the use of this module. 
Use at your own risk. Please backup any critical data before proceeding. For any query or 
assistance, please email support@molpay.com


Installations for CS-Cart MOLPay Plugin/Extension
------------------------------------------------------
[CS-Cart Version 4.3.1 and above] (https://github.com/MOLPay/CSCart_Plugin/wiki/CS-Cart-Version-4.3.x-and-above)

**Attention :**

Since CS-Cart Version 4.1.5 have a ``skey`` conflict with system parameter, then please add this snippet **&& empty($_REQUEST['domain']) && empty($_REQUEST['currency'])** into your cscart root folder (``app``/``controllers``/``frontend``/``init.php``)

``Instruction``

1. Open file ``init.php`` then find **if(!empty($_REQUEST['skey']))** on line 24.

2. Paste the script after **if(!empty($_REQUEST['skey']))**.

3. Result will be like this : <br/>
**if(!empty($_REQUEST['skey']) && empty($_REQUEST['domain']) && empty($_REQUEST['currency']))**.


[CS-Cart Version 4.2.3 and below] (https://github.com/MOLPay/CSCart_Plugin/wiki/CS-Cart-Version-4.2.3-and-below)


Contribution
------------

You can contribute to this plugin by sending the pull request to this repository.


Issues
------------

Submit issue to this repository or email to our support@molpay.com


Support
-------

Merchant Technical Support / Customer Care : support@molpay.com <br>
Sales/Reseller Enquiry : sales@molpay.com <br>
Marketing Campaign : marketing@molpay.com <br>
Channel/Partner Enquiry : channel@molpay.com <br>
Media Contact : media@molpay.com <br>
R&D and Tech-related Suggestion : technical@molpay.com <br>
Abuse Reporting : abuse@molpay.com
