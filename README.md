<h1>Code Duplication Analysis Tool for Magento® 2 Extension</h1>

Magento team at official Magento platform is always issuing alerts under Magento EQP standards to maintain the quality of the code in the marketplace for the extensions uploaded by the third party developers. 

The process is just to make sure that end-users who are downloading and installing the marketplaces extension products on their websites must work flawlessly.

This article introduces one of the necessary EQP steps of Magento marketplace quality checking program, which can easily disapprove an extension by marketplace whenever the prescribed code standards do not meet the section 3.1 and 9.1b of marketplace agreement.  

The most crucial part of Magento marketplace EQP check under mentioned section is Code Duplication. Even if a single line of code found as duplicated by EQP. It may result in the violation of the guidelines and extension might be disapproved immediately.

Therefore, code duplication is one of the breaches of the quality parameters, and Magento 2 extension developers are scrappy to know how to save from error generation notification and subsequent rejection of Magento 2 extension submission with following email content:

<i><b>Code quality issues: CPD: This extension contains duplicated code.</b><br />
Additional Comments: UI/Component/Listing/Columns.php duplicates Magento 2's (module-catalog) version of the same file -- use ClassPreferences to avoid duplicating Magento's code.</i>

Our Magento 2 extension development team also has faced the same issue several times. It has compelled us to find out a code duplication analysis program. Thus, we can find all instances of the duplicated code in the code documents of the Magento 2 extensions.

Unfortunately, the market was devoid of ready to use the script. Therefore, our Magento 2 developers have developed a user-friendly tailor-made program to find the traces of any duplicate code existing in the Magento 2 module or code snippets uploaded along with extension package.

Our duplicate code analysis tool for Magento 2 extension is a highly simplified interface available <br />
A - Online as well as <br />
B - Working on your site / your server <br />

<h2>A. How to use tool online</h2>
This tool is available to explore more on http://codeanalysis.labs.mconnectmedia.com/
<br />
Please, visit our online/web interface and follow the required steps to get your Magento 2 extension code duplication analysis done. It is as easy as to upload and click a button to see the results.

<h2>B. How to use tool on your site</h2>
Apart from checking the tool online using our server on the web, we made the tool available to compile the duplication check on any individual, vendor hosting, or local intranet server.

You can download the tool from GitHub repo for own use. Moreover, you can enhance the features as per your need and ideas through customization. 

Prerequisites 
We have set following prerequisites to install the tool locally on your site or server, and those are:
1.	It needs PHP CPD installed on your server.
2.	It requires minimum Magento 2.x.x vanilla codebase to match with your current code.

<h2>C. How to install the tool</h2>
Download the "Code Duplication Analysis Tool" source code to your local machine. You may find folder structure, as depicted below.
<br />
Steps:<br />
1.	Now, create a folder in Magento root directory and named it as "codeduplication."<br />
2.	Afterwards, place the "Code Duplication Analysis Tool" files in "codeduplication" folder.<br />
3.	It requires to copy the files and folders from the Vendor > ‘Magento’ folder to "Magento-versions" folder. However, we already have created all version zip file from - http://codeanalysis.labs.mconnectmedia.com/download.php <br />

<img src="http://codeanalysis.labs.mconnectmedia.com/github/m2version.png" />

4.	The next step is to assign 0777 permission to following folders <br />
   a.	"Magento-versions"<br />
   b.	"reports" <br />
   c.	"vendor"<br />

Navigate to the web browser and access the folder. 

<h2>Steps to Follow Front-end Interface</h2>
<h4>Step 1: Select Your Magento Version:</h4>
The tool is comparing the code of core Magento matching with your Magento version. Therefore, it is imperative for us to know your Magento version at first place.

So please provide exact Magento version for that you have developed the extension.

<h4>Step 2(a)</h4>
After the selection of your Magento version, the next field input is uploading of extension zip that you want to check for the code duplication analysis using our interface.

Now, choose the zip file from your local machine and upload it to our server to run comparative analysis to find out the trace of any duplicate content in it.

<img src="http://codeanalysis.labs.mconnectmedia.com/github/Step–2a.png" />

Or

<h4>Step 2(b)</h4>
For those developers who would like to check the code of their extension directly, they can copy-paste the code and get an analysis done.

Note: Remember, it requires minimum 13 lines of code to run the module. Moreover, the developer needs to insert the code that begins with PHP tag and only PHP file is allowed, not .phtml or .html files.

In some cases, you may not find individual function. Therefore, for accurate results in analysis, the developer needs to insert the complete file code. Otherwise, uploading of the entire extension zip file is the best alternative.

<img src="http://codeanalysis.labs.mconnectmedia.com/github/Step–2b.png" />

Once you have done, please click on “BEGIN CODE AUDIT” button below it.

<h3>Examples of Expected Results</h3>
For the sake of comprehensibility, we have produced an example of expected results that may display in the format given below.

<img src="http://codeanalysis.labs.mconnectmedia.com/github/reports.png" />

If you face any issue using the tool, please contact us for immediate help. Thanks.

---

M-Connect Media is one of the reputed and award-winning Magento development companies, and it is providing end-to-end ecommerce solutions on Magento® platform.

We have accumulated a pool of highly expert and experienced Magento talents in the form of ecommerce designers, programmers, QA team, and excellent marketers to provide full-cycle Magento ecommerce development services. 


Thanks,<br/>
M-Connect Media <br/>
353 McCook Cir NW, Kennesaw, Georgia 30144, United States.<br/>
https://www.mconnectmedia.com <br/>
Phone: +1 319 804-8627
