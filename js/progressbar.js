function progressbar(current,total){
    if (current > total){
        console.log('Current progress cannot be bigger than total porogress');
        $('#progressbar').hide();
        return;
    }
    if (current <1){
        console.log('Current progress cannot be bigger than total porogress');
        $('#progressbar').hide();
        return;
    }

    currentpercentage = current/total * 100;

    $('#progressbar .currentprogress').width(currentpercentage+'%');
   
}