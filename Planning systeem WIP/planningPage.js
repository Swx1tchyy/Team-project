 const form = document.getElementById('ticketForm');
        const ticketList = document.getElementById('ticketList');

        form.addEventListener('submit', function(e) {
            e.preventDefault();

         
            const name = document.getElementById('fname').value.trim();
            const beschrijving = document.getElementById('beschrijving').value.trim();
            const tijd = document.getElementById('tijd').value.trim();

           
            const placeholder = document.querySelector('.placeholder');
            if (placeholder) placeholder.remove();

      
            const ticket = document.createElement('div');
            ticket.classList.add('ticket');
            ticket.innerHTML = `
                <h3>${name}</h3>
                <p><strong>Beschrijving:</strong> ${beschrijving}</p>
                <p><strong>Tijd:</strong> ${tijd}</p>
            `;

         
            ticketList.appendChild(ticket);

        
            form.reset();
        });
