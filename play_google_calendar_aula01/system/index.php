<form class="main_form" action="add_appointment" method="post" enctype="multipart/form-data">
    <label class="label">
        <span>Título do Agendamento:</span>
        <input name="appointment_title" type="text" class="font_large" placeholder="O que gostaria de agendar?" required>
    </label>

    <label class="label">
        <span>Descrição:</span>
        <textarea rows="4" name="appointment_description"></textarea>
    </label>

    <div class="form_row_50">
        <label class="label_50">
            <span>Localização:</span>
            <input name="appointment_location" type="text" placeholder="Onde será?">
        </label>

        <label class="label_50">
            <span>E-mail:</span>
            <input name="appointment_email" type="email" placeholder="Qual o e-mail do participante?">
        </label>
    </div>

    <label class="label">
        <span>Selecione um dia e horário:</span>
        <select name="appointment_schedule_id">
            <option selected disabled value="">Nenhum Selecionado</option>
        </select>
    </label>

    <p>
        <button type="submit" class="btn">Marcar Consulta!</button>
    </p>
</form>