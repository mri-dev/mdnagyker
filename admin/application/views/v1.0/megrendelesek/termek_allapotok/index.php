<div style="float:right;">
	<a href="/<?=$this->gets[0]?>/" class="btn btn-default"><i class="fa fa-arrow-left"></i> vissza</a>
    <a href="/<?=$this->gets[0]?>/allapotok" class="btn btn-default"><i class="fa fa-bars"></i> Megrendelés állapotok</a>
</div>
<h1>Megrendelt termék állapotok</h1>
<script type="text/javascript">
 
</script>
<?=$this->emsg?>
<? if($this->gets[2] == 'torles'): ?>
<form action="" method="post">
<input type="hidden" name="delId" value="<?=$this->gets[3]?>" />
<div class="row">
	<div class="col-md-12">
    	<div class="panel panel-danger">
        	<div class="panel-heading">
            <h2><i class="fa fa-times"></i> Termék állapot törlése</h2>
            </div>
        	<div class="panel-body">
            	<div style="float:right;">
                	<a href="/<?=$this->gets[0]?>/<?=$this->gets[1]?>" class="btn btn-danger"><i class="fa fa-times"></i> NEM</a>
                    <button class="btn btn-success">IGEN <i class="fa fa-check"></i> </button>
                </div>
            	<strong>Biztos, hogy törli a kiválasztott termék állapotot? (nem ajánlott)</strong></div>
        </div>
    </div>
</div>
</form>
<? else: ?>

<div class="row">
	<div class="col-md-12">
    	<div class="con <?=($this->gets[2] == 'szerkeszt')?'edit':''?>">
        	<form action="" method="post" enctype="multipart/form-data">
        	<h2><? if($this->gets[2] == 'szerkeszt'): ?>Megrendelt termék állapot szerkesztése<? else: ?>Új megrendelt termék állapot hozzáadása<? endif; ?></h2>
            <br>
            <div class="row">
              <div class="col-md-2">
              		Sorrend:<input type="number" name="sorrend" value="<?=($this->err)?$_POST[sorrend]:$this->sm[sorrend]?>" class="form-control">
               	</div>
                <div class="col-md-5  <?=($this->err && $_POST[nev] == '')?'has-error':''?>">
              		Elnevezés:<input type="text" name="nev" class="form-control" value="<?=($this->err)?$_POST[nev]:$this->sm[nev]?>"/>
               	</div>
              <div class="col-md-2">
              		Szín:<input type="text" name="szin" placeholder="pl.: #000000" class="form-control" value="<?=($this->err)?$_POST[szin]:$this->sm[szin]?>"/>
               	</div>
              <div class="col-md-3" align="right">
                	<br>
               	<? if($this->gets[2] == 'szerkeszt'): ?>
                    <input type="hidden" name="id" value="<?=$this->gets[3]?>" />
                    <a href="/<?=$this->gets[0]?>/<?=$this->gets[1]?>"><button type="button" class="btn btn-danger btn-3x"><i class="fa fa-arrow-circle-left"></i> bezár</button></a>
                    <button name="save" class="btn btn-success">Változások mentése <i class="fa fa-check-square"></i></button>
                    <? else: ?>
                    <button name="add" class="btn btn-primary">Hozzáadás <i class="fa fa-check-square"></i></button>
                    <? endif; ?>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
<div class="row">
	<div class="col-md-12">
    	<div class="con">
        	<h2>Megrendelt termék állapotok</h2>
            <br />
            <div class="row" style="color:#aaa;">
            	<div class="col-md-1">
                	<em>#</em>
                </div>
                <div class="col-md-1">
                	<em>Sorrend</em>
                </div>
                <div class="col-md-5">
              		<em>Elnevezés</em>
                </div>
                <div class="col-md-3">
                	<em>Szín</em>
                </div>
                <div class="col-md-2" align="right">
                    <em></em>
                </div>
           	</div>
        	<? if(count($this->o)> 0): foreach($this->o as $d): ?>
            <br />
            <div class="row markarow">
            	<div class="col-md-1">
                	<?=$d[ID]?>
                </div>
                <div class="col-md-1">
                	<?=$d[sorrend]?>
                </div>
                <div class="col-md-5">
              		<strong style="color:<?=$d[szin]?>;"><?=$d[nev]?></strong>
                </div>
                <div class="col-md-3">
                	<span style="background:<?=$d[szin]?>;">&nbsp;&nbsp;&nbsp;</span> <em><?=$d[szin]?></em>
                </div>
                <div class="col-md-2" align="right">
                    <a href="/<?=$this->gets[0]?>/<?=$this->gets[1]?>/szerkeszt/<?=$d[ID]?>" title="Szerkesztés"><i class="fa fa-pencil"></i></a>&nbsp;&nbsp;
                    <a href="/<?=$this->gets[0]?>/<?=$this->gets[1]?>/torles/<?=$d[ID]?>" title="Törlés"><i class="fa fa-times"></i></a>
                </div>
           	</div>
            <? endforeach; else:?>
            	<div class="noItem">
                	Nincs létrehozott állapot!
                </div>
            <? endif; ?>
            
        </div>
    </div>
</div>
<? endif; ?>