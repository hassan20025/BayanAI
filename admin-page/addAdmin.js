// Add Admin Modal logic
document.addEventListener('DOMContentLoaded', function() {
  const addAdminBtn   = document.getElementById('add-admin-btn');
  const addAdminModal = document.getElementById('add-admin-modal');
  const closeModalBtn = document.getElementById('close-modal-btn');
  const addAdminForm  = document.getElementById('add-admin-form');

  if (addAdminBtn)   addAdminBtn.onclick   = () => addAdminModal.classList.add('active');
  if (closeModalBtn) closeModalBtn.onclick = () => addAdminModal.classList.remove('active');

  if (addAdminModal) {
    addAdminModal.addEventListener('click', (e) => {
      if (e.target === addAdminModal) addAdminModal.classList.remove('active');
    });
  }

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && addAdminModal?.classList.contains('active')) {
      addAdminModal.classList.remove('active');
    }
  });

  if (addAdminForm) {
    addAdminForm.onsubmit = async (e) => {
      e.preventDefault();
  
      // Build from form (uses name="...")
      const formData = new FormData(addAdminForm);
  
      // FORCE include/overwrite password in case the browser skips it
      const passEl = document.getElementById('admin-password');
      formData.set('password', passEl ? passEl.value : '');
  
      // Debug: confirm what's going out
      const entries = [...formData.entries()];
      console.log('FormData entries:', entries.map(([k, v]) => k === 'password' ? [k, '***'] : [k, v]));
  
      try {
        const res = await fetch('/bayanAI/api/addAdmin.php', {
          method: 'POST',
          headers: { 'X-CSRF-Token': window.CSRF_TOKEN },
          body: formData
        });
        
        if (!res.ok) throw new Error(`HTTP error ${res.status}`);
  
        const data = await res.json();
        console.log('Server response:', data);
  
        if (data.error) {
          alert(data.error);
          return;
        }
  
        if (typeof refreshAdminsTable === 'function') await refreshAdminsTable();
        addAdminModal.classList.remove('active');
        addAdminForm.reset();
      } catch (err) {
        console.error(err);
        alert('Error adding admin: ' + err.message);
      }
    };
  }
  
});
