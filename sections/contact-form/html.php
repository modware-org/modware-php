<div class="contact-form">
    <div class="container">
        <h2>Formularz kontaktowy</h2>
        <form id="contactForm" action="/api/contact" method="POST">
            <div class="form-group">
                <label for="name">Imię i nazwisko</label>
                <input type="text" id="name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="subject">Temat</label>
                <input type="text" id="subject" name="subject" required>
            </div>
            
            <div class="form-group">
                <label for="message">Wiadomość</label>
                <textarea id="message" name="message" rows="5" required></textarea>
            </div>
            
            <div class="form-group">
                <button type="submit" class="submit-btn">Wyślij wiadomość</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('contactForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());
    
    try {
        const response = await fetch('/api/contact', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });
        
        if (response.ok) {
            alert('Wiadomość została wysłana. Dziękujemy za kontakt!');
            e.target.reset();
        } else {
            throw new Error('Wystąpił błąd podczas wysyłania wiadomości.');
        }
    } catch (error) {
        alert('Przepraszamy, wystąpił błąd. Prosimy spróbować później.');
        console.error('Error:', error);
    }
});
</script>
