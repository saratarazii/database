document.addEventListener('DOMContentLoaded', function() {
    const title = document.getElementById('title');
    const frame1 = document.getElementById('frame1');
    const frame2 = document.getElementById('frame2');
    
    // Click handler for the title
    title.addEventListener('click', function() {
        // Add fade-out animation to frame1
        frame1.classList.add('fade-out');
        
        // After animation completes, hide frame1 and show frame2
        setTimeout(() => {
            frame1.classList.add('hidden');
            frame2.classList.remove('hidden');
            frame2.classList.add('fade-in');
            
            // Remove animation classes after they complete
            setTimeout(() => {
                frame1.classList.remove('fade-out');
                frame2.classList.remove('fade-in');
            }, 300);
        }, 300);
    });
});