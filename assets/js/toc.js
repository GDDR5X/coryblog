document.addEventListener('DOMContentLoaded', function() {
    const content = document.querySelector('.content-body');
    const tocList = document.querySelector('.toc-list');
    
    if (!content || !tocList) return;

    // 1. Find all headers
    const headers = content.querySelectorAll('h1, h2, h3, h4');
    if (headers.length === 0) {
        document.querySelector('.post-sidebar').style.display = 'none';
        document.querySelector('.post-container').style.gridTemplateColumns = '1fr';
        return;
    }

    // 2. Generate TOC
    headers.forEach((header, index) => {
        // Generate ID if missing
        if (!header.id) {
            header.id = 'heading-' + index;
        }

        const li = document.createElement('li');
        const a = document.createElement('a');
        a.href = '#' + header.id;
        a.textContent = header.textContent;
        
        // Add class based on tag name (h2, h3, etc.)
        li.className = 'toc-' + header.tagName.toLowerCase();
        
        li.appendChild(a);
        tocList.appendChild(li);
    });

    // 3. Scroll Spy (Highlight active TOC item)
    const observerOptions = {
        root: null,
        rootMargin: '-100px 0px -60% 0px', // Adjust active zone
        threshold: 0
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            const id = entry.target.getAttribute('id');
            const link = tocList.querySelector(`a[href="#${id}"]`);
            
            if (entry.isIntersecting) {
                // Remove active class from all
                tocList.querySelectorAll('a').forEach(l => l.classList.remove('active'));
                // Add to current
                if (link) link.classList.add('active');
            }
        });
    }, observerOptions);

    headers.forEach(header => observer.observe(header));

    // Smooth scroll for TOC links
    tocList.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                const headerOffset = 100; // Match sticky header height
                const elementPosition = targetElement.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
    
                window.scrollTo({
                    top: offsetPosition,
                    behavior: "smooth"
                });
                
                // Update active state immediately
                tocList.querySelectorAll('a').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
                
                // Update URL hash without jumping
                history.pushState(null, null, '#' + targetId);
            }
        });
    });
});
