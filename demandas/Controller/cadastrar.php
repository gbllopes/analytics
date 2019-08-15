<?php	
		include_once '../Database/database.php';
		$db = new Database();
		$demandante			= $_POST['demandante'];
		$matricula  		= $_POST['matricula'];
		$fone_ramal			= $_POST['fone_ramal'];
		$divisao				= $_POST['divisao'];
		$assunto				= $_POST['assunto'];
		$classificacao 	= $_POST['classificacao'];
		$necessidades		= $_POST['necessidades'];
		$condicoes			= $_POST['condicoes']; 
		$campos_resultados	= $_POST['campos_resultados'];
		$responsavel 	= $_POST['responsavel'];
		$data_us =  date('Y-m-d'); 
		$data_br = date('d-m-Y');		
		
		// verifica se o ano mudou e cria o numero da demanda.
		$ano = date('Y');
		$max = mysqli_query($db->getConection(), "SELECT MAX(ano_cont) FROM ano_prot") or die (mysql_error());;
		$co3 = mysqli_fetch_assoc(mysqli_query($db->getConection(), 'SELECT MAX(ano_cont) FROM ano_prot'));
		$max = intval($co3['MAX(ano_cont)']);

		if ($max < $ano) {
					mysqli_query ($db->getConection(), "INSERT INTO ano_prot (ano_cont) VALUES ($ano) ");
					$protocolo =  date("Y") .  "0001";
				
		}else{
					$co = mysqli_fetch_assoc(mysqli_query($db->getConection(), 'SELECT MAX(num_demanda) FROM demandas_mining'));
					$protocolo = intval($co['MAX(num_demanda)']) + 1;					
		}
				
	  // Verifica se o submit contem algum campo vazio, caso não, adiciona a nova demanda ao banco de dados.
		
    if(empty($demandante) || empty($matricula)|| empty($fone_ramal) || empty($divisao) || empty($assunto) || empty($necessidades)|| empty($condicoes)|| empty($campos_resultados)){
        $msgm = false;
    }else{        
		$sql = "select id, divisao from divisoes a inner join gerencias b on b.id = a.id_gerencia where id_divisao = ".$divisao;
		$res = mysqli_query($db->getConection(), $sql);
		foreach($res as $r){
			$gerencia = $r['id'];
			$divisao  = $r['divisao'];
		}
        $sql_add_tarefa = "insert into demandas_mining ( num_demanda, demandante, matricula, fone_ramal, divisao, assunto, responsavel,necessidades, condicoes, campos_resultados, entregue, data_registro, data_registro_txt, id_gerencia, classificacao)
									VALUES ('".$protocolo."', '".utf8_decode($demandante)."', '".utf8_decode($matricula)."', '".utf8_decode($fone_ramal)."', '".utf8_decode($divisao)."', '".utf8_decode($assunto)."','".utf8_decode($responsavel)."', '".utf8_decode($necessidades)."', '".utf8_decode($condicoes)."', '".utf8_decode($campos_resultados)."', 'Não', '".utf8_decode($data_us)."', '".$data_br."', $gerencia, '".utf8_decode($classificacao)."'
									)";				
		$sql = mysqli_query($db->getConection(), $sql_add_tarefa);
		if($res){
			$msgm = true;
		}    
	}
	echo $msgm;

?>
	

