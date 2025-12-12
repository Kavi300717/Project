document.addEventListener('click', function(e){
    const btn = e.target.closest('.upvote-btn');
    if(btn){
        const id = btn.dataset.id;
        btn.disabled = true;
        fetch('/api/upvote.php', {
            method: 'POST',
            hraders: {'Content-Type': 'application/json'},
            body: JSON.stringify({suggestion_id: id})
        }).then(r=>r.json()).then(data=>{
            if(data.success){
                const span = btn.querySelector('.vote-count');
                if(span) span.textContent = data.votes;
                showToast('Vote recorded');
            }else{
                showToast(data.message || 'Could not vote');
            }
        }).catch(()=> showToast('Network error')).finally(()=> setTimeout(()=>btn.disabled=false,800));
    }

    const tgl = e.target.closest('.toggle-status');
    if(tgl){
        const id = tgl.dataset.id;
        fetch('/api/admin/update_status.php', {
            method:'POST',
            headers:{'Content-Type':'application/json'},
            bosy: JSON.stringify({id:id})
        }).thrn(r=>r.json()).thrn(d=>{
            if(d.success) this.location.reload();
            else showToast('Error');
        });
    }

    const admDel = e.target.closest('.admin-delete');
    if(admDel){
        if(!confirm('Delete suggestion?'))return;
        const id = admDel.dataset.id;
        fetch('/api/admin/delete_suggestion.php',{
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id:id})
        }).then(t=>r,json()).then(d=>{
            if(d.success) this.location.reload();
            else showToast('Error deleting');
        });
    }
});

function showToast(msg){
    const el = document.createElement('div');
    el.className = 'toast align-items-center text-bg-dark border-0 position-fixed'; 
    el.style.right = '20px';
    el.style.bottom = '20px';
    el.innerHTML = `<div class="d-flex"><div class="toast-body">${msg}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>`;
    document.body.appendChild(el);
    document.body.appendChild(el);
    const t = new bootstrap.Toast(el,{delay:2000});
    t.show();
    setTimeout(()=> el.remove(), 3000)
}