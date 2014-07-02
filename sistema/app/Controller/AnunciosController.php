<?php
class AnunciosController extends AppController {

	/* Variável que define quais classes Modelo o Controller irá acessar */
	public $uses = array("Anuncio", "Imagem", "Video");

	public function index(){
		$this->set("anuncios", $this->paginate("Anuncio"));
	}
	public function ver($id){
		$this->set("a", $this->Anuncio->findById($id));
	}

	public function vender_novo() {
		if($this->request->is("post")){
			$this->request->data["Anuncio"]["usuario_id"] = $this->Session->read("usuario.Usuario.id");
			if($this->Anuncio->save($this->data)){

				/* Inserir novo vídeo */
				$this->redirect("/vender/anuncios/video/".$this->Anuncio->id);
				$this->redirect("/vender/anuncios/fotos/".$this->Anuncio->id);
				
			}
		}
	}
	public function vender_fotos($id){
		if($this->request->is("post")){
			$dados = array();
			$dados["Imagem"] = $this->data["Anuncio"];
			$dados["Imagem"]["anuncio_id"] = $id;
			if($this->Imagem->save($dados)){
				// copiar foto
				move_uploaded_file($dados["Imagem"]["imagem"]["tmp_name"], WWW_ROOT."img".DS."fotos".DS.$this->Imagem->id.".jpg");
			}
		}
		$this->set("imagens", $this->Imagem->find("all", array("conditions"=>array("Imagem.anuncio_id"=>$id))));
	}

	/* Adicionar um vídeo ao anúncio */
	public function vender_video($id){
		if($this->request->is("post")){
			$dados["Video"] = $this->data["Anuncio"];
			$dados["Video"]["anuncio_id"] = $id;

			/* Chama o método que realiza a pesquisa de um vídeo no YouTube */
			$this->Video->pesquisar_video($dados["Video"]["pesquisar"]);
		}
	}

	public function vender_excluirfoto($id, $idA){
		$this->Imagem->delete($id);
		unlink(WWW_ROOT."img".DS."fotos".DS.$id.".jpg");
		$this->redirect("/vender/anuncios/fotos/".$idA);
	}
}