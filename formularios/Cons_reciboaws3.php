<?php
$Nmenu = 482;
$titulo = " Enviar Recibos a AWS S3 ";

require_once('autentificacion/aut_verifica_menu.php');
$bd = new DataBase();

// --- LÓGICA DE PERIODOS PERMITIDOS ---
$fecha_actual = new DateTime();
$fecha_anterior = (new DateTime())->modify('-1 month');

// Creamos un array con las dos opciones permitidas
$periodos = [
    [
        "anio" => $fecha_actual->format('Y'),
        "mes_num" => $fecha_actual->format('n'),
        "mes_nombre" => strftime('%B', $fecha_actual->getTimestamp()) // O usar array manual
    ],
    [
        "anio" => $fecha_anterior->format('Y'),
        "mes_num" => $fecha_anterior->format('n'),
        "mes_nombre" => strftime('%B', $fecha_anterior->getTimestamp())
    ]
];

// Nombres de meses manual para evitar problemas de locale en el servidor
$meses_nombres = [
    1 => "Enero", 2 => "Febrero", 3 => "Marzo", 4 => "Abril", 
    5 => "Mayo", 6 => "Junio", 7 => "Julio", 8 => "Agosto", 
    9 => "Septiembre", 10 => "Octubre", 11 => "Noviembre", 12 => "Diciembre"
];

$sql_contractos = "SELECT codigo, descripcion FROM contractos WHERE status = 'T' ORDER BY 2 ASC";
?>
<script language="javascript">


function enviarDatosWebhook() {
    // 1. Capturamos los valores de los selectores por sus IDs
    const datos = {
        anio: document.getElementById('co_cont').value,
        mes: document.getElementById('mes_cont').value,
        quincena: document.getElementById('quincena').value,
        contrato: document.getElementById('co_contrato').value,
        test: false
    };

    // 2. Validación básica: evitar enviar si faltan campos
    if (!datos.anio || !datos.mes || !datos.quincena || !datos.contrato) {
        alert("Por favor, complete todos los campos antes de procesar.");
        return;
    }

    // 3. Configuración del envío al endpoint de n8n
    const url = 'http://212.56.33.4:5678/webhook/api/v1/procesar-recibos';

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(datos) // Convertimos el objeto JS a JSON
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la conexión con el servidor');
        }
        return response.json();
    })
    .then(data => {
        console.log('Éxito:', data);
        alert("¡Proceso iniciado correctamente en n8n!");
    })
    .catch((error) => {
        console.error('Error:', error);
        alert("Hubo un problema al conectar con el servicio de recibos.");
    });
}

</script>

<br>
<div align="center" class="etiqueta_title"> <?php echo $titulo; ?> </div>
<br/>
<form name="form_recibo" id="form_recibo">
    <table width="500px" border="0" align="center">
        <tr>
            <td class="etiqueta" width="40%">Año:</td>
            <td id="select01">
                <select name="co_cont" id="co_cont" style="width:200px;">
                    <option value="">Seleccione Año..</option>
                    <?php
                        // Solo mostramos los años de los dos periodos (evita duplicados con unique)
                        $anios_unicos = array_unique([$periodos[0]['anio'], $periodos[1]['anio']]);
                        foreach ($anios_unicos as $a) {
                            echo '<option value="' . $a . '">' . $a . '</option>';
                        }
                    ?>
                </select>
            </td>
        </tr>

        <tr>
            <td class="etiqueta">Mes:</td>
            <td id="select02">
                <select name="mes_cont" id="mes_cont" style="width:200px;">
                    <option value="">Seleccione Mes..</option>
                    <?php
                        // Mostramos solo el mes actual y el anterior
                        foreach ($periodos as $p) {
                            echo '<option value="' . $p['mes_num'] . '">' . $meses_nombres[$p['mes_num']] . '</option>';
                        }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td class="etiqueta"><?php echo "Quincena" ?>:</td>
            <td id="select03">
                <select name="quincena" id="quincena" style="width:200px;">
                    <option value="">Seleccione Quincena..</option>
                    <option value="1">1era Quincena</option>
                    <option value="2">2da Quincena</option>
                </select>
                <br><span class="selectRequiredMsg">Debe Seleccionar una Quincena.</span>
            </td>
        </tr>

        <tr>
            <td class="etiqueta"><?php echo "Contrato" ?>:</td>
            <td id="select04">
                <select name="co_contrato" id="co_contrato" style="width:200px;">
                    <option value="">Seleccione Contrato..</option>
               	  <?php
				    $query = $bd->consultar($sql_contractos);
                     while($row02=$bd->obtener_fila($query,0)){
						   echo '<option value="'.$row02[0].'">'.$row02[1].'</option>';
					 }?>
                </select>
                <br><span class="selectRequiredMsg">Debe Seleccionar un Tipo de Contrato.</span>
            </td>
        </tr>
        <tr>
            <td class="etiqueta">Tipo de Documento:</td>
            <td>
                <input type="checkbox" name="tipo_recibo" id="tipo_recibo" value="recibo"> 
                <label for="tipo_recibo">Recibos</label>
                <br>
                <input type="checkbox" name="tipo_cestaticket" id="tipo_cestaticket" value="cestaticket"> 
                <label for="tipo_cestaticket">Cestaticket Humani</label>
            </td>
        </tr>
    </table>
    <br>

    <div align="center">  
        <span class="art-button-wrapper">
                        <span class="art-button-l"> </span>
                        <span class="art-button-r"> </span>
                    <input type="button" name="salvar"  id="salvar" value="Enviar" onclick="enviarDatosWebhook()" class="readon art-button" />
                    </span>&nbsp;
                <span class="art-button-wrapper">
                        <span class="art-button-l"> </span>
                        <span class="art-button-r"> </span>
                    <input type="reset" id="limpiar" value="Restablecer" class="readon art-button" />
                    </span>&nbsp;
                <span class="art-button-wrapper">
                        <span class="art-button-l"> </span>
                        <span class="art-button-r"> </span>
                    <input type="button" id="volver" value="Volver" onClick="history.back(-1);" class="readon art-button" />
                    </span>
        <input type="hidden" id="usuario" value="<?php echo $usuario;?>"/>

    </div>
</form>
<br />
<br />
<div align="center">
</div>
<script type="text/javascript">
	var select01 = new Spry.Widget.ValidationSelect("select01", {validateOn:["blur", "change"]});
</script>
