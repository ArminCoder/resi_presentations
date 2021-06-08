var step = 1;

window.addEventListener('keydown', (event) => {
    if(event.key === 'ArrowRight') {
        document.getElementById('nextButton').click();
    } 
    else if (event.key === 'ArrowLeft') {
        document.getElementById('prevButton').click();
    }
    
    if (event.key === 'ArrowDown'){  
            document.getElementById('step'+ step).style.display = 'block';
            step++;    
    }
});


