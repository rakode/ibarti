<script language="javascript">

function enviarDatosWebhook() {
    // 1. Capturamos los valores de los selectores por sus IDs
    const datos = {
        anio: document.getElementById('co_cont').value,
        mes: document.getElementById('mes_cont').value,
        quincena: document.getElementById('quincena').value,
        contrato: document.getElementById('co_contrato').value
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
<?php
$Nmenu = 732;
$titulo = " Enviar Recibos a WS3 ";
$archivo = "enviarrecibosnubews3";

$vinculo = "inicio.php?area=formularios/Cons_".$archivo."_det&Nmenu=$Nmenu&mod=".$_GET['mod']."";
require_once('autentificacion/aut_verifica_menu.php');
$bd = new DataBase();

$sql_contractos = "SELECT contractos.codigo, contractos.descripcion AS contracto
                     FROM usuario_roles , trab_roles, ficha , contractos
			        WHERE usuario_roles.cod_usuario = '$usuario'
			          AND usuario_roles.cod_rol = trab_roles.cod_rol
				      AND trab_roles.cod_ficha = ficha.cod_ficha
				      AND ficha.cod_contracto = contractos.codigo
			     GROUP BY contractos.codigo
			     ORDER BY 2 ASC ";
?>
<br>
<div align="center" class="etiqueta_title"> ENVAR RECIBOS A LA NUBE WS3</div>
<div id="Contendor01" class="mensaje"></div>
<br/>
<form name="form01_apertura" id="form01_apertura" action="<?php echo $vinculo;?>" method="post">
    <table width="500px" border="0" align="center">
        <tr>
            <td height="8" colspan="2" align="center"><hr></td>
        </tr>

        <tr>
            <td class="etiqueta" width="40%"><?php echo "Año" ?>:</td>
            <td id="select01">
                <select name="co_cont" id="co_cont" style="width:200px;">
                    <option value="">Seleccione Año..</option>
                    <?php
                        $anio_actual = date("Y");
                        $anio_inicio = $anio_actual - 5;
                        $anio_fin = $anio_actual + 1;
                        for ($i = $anio_fin; $i >= $anio_inicio; $i--) {
                            echo '<option value="' . $i . '">' . $i . '</option>';
                        }
                    ?>
                </select>
                <br><span class="selectRequiredMsg">Debe Seleccionar Un Campo.</span>
            </td>
        </tr>

        <tr>
            <td class="etiqueta"><?php echo "Mes" ?>:</td>
            <td id="select02">
                <select name="mes_cont" id="mes_cont" style="width:200px;">
                    <option value="">Seleccione Mes..</option>
                    <?php
                        $meses = [
                            "1" => "Enero", "2" => "Febrero", "3" => "Marzo", 
                            "4" => "Abril", "5" => "Mayo", "6" => "Junio",
                            "7" => "Julio", "8" => "Agosto", "9" => "Septiembre", 
                            "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre"
                        ];
                        foreach ($meses as $valor => $nombre) {
                            echo '<option value="' . $valor . '">' . $nombre . '</option>';
                        }
                    ?>
                </select>
                <br><span class="selectRequiredMsg">Debe Seleccionar un Mes.</span>
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
    </table>

 
<div align="center">  <span class="art-button-wrapper">
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
