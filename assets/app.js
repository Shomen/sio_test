/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

//initiating start point
let startPoint = 0;
let interval = '';
let startTime = '';
let endTime = '';
let curProject = '';

function timerFunc(){
  startPoint++;

  // Get hours
  let hours = Math.floor(startPoint / 3600);
  // Get minutes
  let minutes = Math.floor((startPoint - hours * 3600) / 60);
  // Get seconds
  let seconds = Math.floor(startPoint % 60);

  if (hours < 10) {
    hours = `0${hours}`;
  }
  if (minutes < 10) {
    minutes = `0${minutes}`;
  }
  if (seconds < 10) {
    seconds = `0${seconds}`;
  }
  let curtime = `${hours}:${minutes}:${seconds}`;

  jQuery("#test-running-time").html(curtime);

}

jQuery(document).ready(function(){
    jQuery('#test-tracking-start').on('click', function(){
        if(curProject){ // Is task selected
            // Start time
            const d = new Date();
            let hour = d.getHours();
            let minutes = d.getMinutes();
            let seconds = d.getSeconds();
            startTime = hour + ':' + minutes + ':' + seconds;
      
            jQuery(this).css('display', 'none');
            jQuery('#test-tracking-end').css('display', 'block');
            if (interval) {
              return;
            }
      
            interval = setInterval(timerFunc, 1000);
          }else{
            alert('Please select a project to start timer');
          }
    });

    jQuery('#test-tracking-end').on('click', function(){
        // End time
        const d = new Date();
        let hour = d.getHours();
        let minutes = d.getMinutes();
        let seconds = d.getSeconds();
        endTime = hour + ':' + minutes + ':' + seconds;

        jQuery.ajax({
            url: 'savetime',
            method: 'POST',
            processData: false,
            contentType: "application/json; charset=utf-8",
            data: JSON.stringify({ "projectID": curProject, "starttime": startTime, "endtime": endTime }),
            dataType: "json",
            success: function (data) {
                //console.log(data);
                // Reseting all
                jQuery('#test-tracking-end').css('display', 'none');
                jQuery('#test-tracking-start').css('display', 'block');
                clearInterval(interval);
                startPoint = 0;
                interval = '';
                startTime = '';
                endTime = '';        
                jQuery("#test-running-time").html('00:00:00');
            }
        });
    });
    
    //Project working on
    jQuery(".test-checkbox-projectid").on('click', function (e) {
        
        if(jQuery(this).is(":checked")){
            jQuery(".test-checkbox-projectid").prop("checked", false);
            jQuery(this).prop("checked", true);
            let projectId = jQuery(this).attr('data');
            curProject = projectId; // Update current project id    
           
        }else{
            curProject ='';
        }
    });

}); // End ready
