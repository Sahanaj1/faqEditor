<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script>
  var faqEditorContainer = document.getElementById('faqEditorContainer');
  if (faqEditorContainer != null) {
    faqEditorContainer.style.display = "none";
  }

  var closeFaqEditContentButton = document.getElementById("closeEditContentBtn");
  if (closeFaqEditContentButton != null) {
    closeFaqEditContentButton.onclick = function () {
      if(myUrl.searchParams.get("editmode") == 'uZiIW78vKu') {
        disableEditorContainer();
      } else if(myUrl.searchParams.get("editmode") == 'fZiIW78vKu') {
        disableFaqEditorContainer();
      }
    }
  }

  var oldContent;

  function concatenateElements(arr) {
    let result = [];
    let currentElement = arr[0];
    for (let i = 1; i < arr.length; i++) {
      if (currentElement.includes("?")) {
        result.push(currentElement);
        currentElement = arr[i];
      } else {
        if (arr[i].includes("?")) {
          result.push(currentElement);
          currentElement = arr[i];
        } else {
          currentElement += "<br>" + arr[i];
        }
      }
    }
    result.push(currentElement);
    return result;
  }

  function prepareQuesAnsFromEditorHTML(faqArray) {

    let IntermediateQuesAnsArray = [];
    var question;
    var Qanswer;
    let len = faqArray.length;
    var i;
    for (i = 0; i < len; i++) {
      question = faqArray[i];
      i++;
      answer = faqArray[i];
      i++;
      if (i < len) {
        while (i < len && !faqArray[i].includes('?')) {
          answer += "<br>" + faqArray[i];
          i++;
        }
      }
      IntermediateQuesAnsArray.push(question);
      IntermediateQuesAnsArray.push(answer);
    }



    let questionNumber = 1;
    for (i = 0; i < IntermediateQuesAnsArray.length; i += 2) {
      IntermediateQuesAnsArray[i] = questionNumber + ". " + IntermediateQuesAnsArray[i];
      questionNumber++;
    }

    let QuesAnswers = IntermediateQuesAnsArray[0].match(/(<p)(.*?)(<\/p>)/gi)
      .map(Qanswer => Qanswer.replace(/(<\/?p>)/gi, ''));

    return QuesAnswers;

  }
function FinalQuesAnsFix(arr){
  const result = [];
for (let i = 0; i < arr.length; i++) {
    if (arr[i].includes("?") && /[a-zA-Z]/.test(arr[i].substr(arr[i].indexOf("?") + 1))) {
        const splitString = arr[i].split("?");
        result.push(splitString[0] + "?");
        result.push(splitString[1]);
    } else {
        result.push(arr[i]);
    }
}
return result;
}
  function finalFaqContentJSON(finalQuesAnsArray) {
    let faqLen = finalQuesAnsArray.length;
    finalJSON = [];
    for (j = 0; j < faqLen; j += 2) {
      var faqObject = { "question": "", "answer": "" };
      faqObject.question = finalQuesAnsArray[j];
      faqObject.answer = finalQuesAnsArray[j + 1];
      finalJSON.push(faqObject);
    }
    return finalJSON;
  }

  function saveFAQEditorIntoDb() {

    let faqArray = document.getElementsByClassName('fr-view')[0].innerHTML;
    faqArray = faqArray.split('\n');

    let Quesanswers = prepareQuesAnsFromEditorHTML(faqArray);

    let finalQuesAnsArray = concatenateElements(Quesanswers);
    
    let QuesAnsCreation=FinalQuesAnsFix(finalQuesAnsArray);

    let faqContentSave = finalFaqContentJSON(QuesAnsCreation);

    let username = getCookie('username');
    let passkey = getCookie('passkey');

    if (username == null || passkey == null) {
      swal.fire("Session Error!", "'Session Expired...Please login Again !!!!'", "error")
      location.reload();
    }
    else {
      $.ajax({
        type: "POST",
        url: "Your DB URL",
        data: { 'faqContentSave': JSON.stringify(faqContentSave), 'contentType': 'faq', url: window.location.href, 'username': username, 'passkey': passkey, 'oldContent': JSON.stringify(oldContent) },
        success: function (response) {

          if (response == 'Invalid User, Please Login Again') {
            deleteCookie('username');
            deleteCookie('passkey');
            swal.fire("Session Error!", response, "error")
          }
          else {
            swal.fire("Content Saved!", response, "success")
          }
          location.reload();
        }
      });
    }
  }

  function validateEditorStatus() {
    if (getCookie('username') == null || getCookie('passkey') == null) {
      swal.fire("Session Error!", "Editor Mode not Enabled, Please login First !!!!", "error")
      return 0;
    }
    else {
      return 1;
    }
  }

  function disableFaqEditorContainer() {
    let faqEditorContainer = document.getElementById('faqEditorContainer');
    let enableEditorButton = document.getElementById("editContentBtn");
    let closeFaqEditContentButton = document.getElementById("closeEditContentBtn");
    if (validateEditorStatus() == 1) {
      faqEditorContainer.style.display = "none";
      deleteCookie('username');
      deleteCookie('passkey');
      closeFaqEditContentButton.style.display = "none";
      location.replace(location.origin + location.pathname)
    }
  }

  function enableFaqEditorContainer() {
    let faqEditorContainer = document.getElementById('faqEditorContainer');
    let enableEditorButton = document.getElementById("editContentBtn");
    let closeFaqEditContentButton = document.getElementById("closeEditContentBtn");
    faqEditorContainer.style.display = "block";
    closeFaqEditContentButton.style.display = "block";
    enableEditorButton.style.display = "none";

  }

  function generateFAQ(faqData) {
    oldContent = faqData;
    const generatedHtml = Object.keys(faqData).reduce(
      (accum, currKey) =>
        accum +
        `<p>${faqData[currKey].question}</p>
         <p>${faqData[currKey].answer}</p>`,
      ""
    );
    document.getElementById('faqsOnEditor').innerHTML = generatedHtml;
  }

</script>
<?php
$haystack = 'your key to maintain secrecy';

if (isset($_GET['editmode'])) {
  $needle = $_GET['editmode'];
  if ($needle != '' && (str_contains($haystack, $needle))) {
    $linkmaps = array();
    $currenturl = strtok($_SERVER['REQUEST_URI'], '?');
    if ((str_contains($currenturl, '/lab-test-')) || (str_contains($currenturl, '/lab-tests-'))) {
     //your table name
    }
    if ((str_contains($currenturl, 'test')) && !(str_contains($currenturl, '/lab-test-')) && !(str_contains($currenturl, '/lab-tests-')) && !(str_contains($currenturl, 'checkup'))) {
      //your table name
    }


    if (str_contains($currenturl, 'bangalore')) {
      //your table name
    }
    if (str_contains($currenturl, 'gurgaon')) {
      //your table name
    }
    if (str_contains($currenturl, 'delhi')) {
       //your table name
    }
    if (str_contains($currenturl, 'noida')) {
      //your table name
    }
    if (str_contains($currenturl, 'hyderabad')) {
     //your table name
    }

    global $wpdb;
    if ($linkmaps['tablepart'] == 'pillar') {
      $result = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . "pillar_pages" . " WHERE path = '" . $currenturl . "'");
    } else {
      if (str_contains($currenturl, '(')) {
        $result = $wpdb->get_results('SELECT * FROM ' . $linkmaps['table'] . ' WHERE path = ' . '"' . $currenturl . ')";');
      } else {
        $result = $wpdb->get_results('SELECT * FROM ' . $linkmaps['table'] . ' WHERE path = ' . '"' . $currenturl . '";');
      }
    }
    echo "<br><a onclick='saveFAQEditorIntoDb()' class='button'><button style='width: 100%; z-index: 999;'>Save</button></a>";
    foreach ($result as $key => $object) {
      echo "
          <link href='https://cdn.jsdelivr.net/npm/froala-editor@latest/css/froala_editor.pkgd.min.css' rel='stylesheet' type='text/css' />

					<div id='froalaFaqEditor' style='height: 300px'>
          <div id='faqsOnEditor'>
          </div>
          <script> generateFAQ($object->faq)</script>
          </div>
          <script type='text/javascript' src='https://cdn.jsdelivr.net/npm/froala-editor@latest/js/froala_editor.pkgd.min.js'></script> 
          <script>var editor = new FroalaEditor('#froalaFaqEditor');</script>";
    }
  }
}
?>