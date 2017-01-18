<?php FB::INFO('__________________________OK'); ?>

<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title></title>
    <meta name="description" content="Personal Kanban Board">
    <meta name="viewport" content="width=device-width">
    <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
    <link rel="stylesheet" href="<?php echo Yii::app()->theme->baseUrl;?>/kanban/styles/640a0960.main.css">
    <link rel="stylesheet" href="<?php echo Yii::app()->theme->baseUrl;?>/kanban/styles/themes/default-bright.css" id="themeStylesheet">
</head>
<body ng-app="mpk" ng-controller="ApplicationController" ui-keyup="{'ctrl-shift-79':'openKanbanShortcut($event)', 'ctrl-shift-72':'openHelpShortcut($event)'}">
    <!--[if lt IE 7]>
      <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->

    <!--[if lt IE 9]>
      <script src="bower_components/es5-shim/es5-shim.js"></script>
      <script src="bower_components/json3/lib/json3.min.js"></script>
    <![endif]-->

    <!-- Add your site or application content here -->
<header class="navbar navbar-fixed-top" role="navigation" id="headerMenu">
  <div class="navbar-inner">
    <div class="container">
      <div class="navbar-header col-md-3">
        <span id="kanbanName" class="navbar-brand" ng-model="kanban" ng-hide="editingName"><a href="#renameKanban" class="renameKanban" ng-click="editingKanbanName()">{{kanban.name}}</a></span>
        <div ng-show="editingName" class="pull-left">
          <form ng-submit="rename()">
            <div class="input-group">
              <span class="input-group-addon">
                <a href="#cancel" ng-click="editingName=false"><i class="glyphicon glyphicon-tasks"></i></a>
              </span>
              <input type="text" name="kanbanName" ng-model="newName" class="form-control">
            </div>
          </form>
        </div>
      </div>
      <div class="col-md-9">
        <ul class="nav navbar-nav pull-right" id="menu" ng-controller="MenuController">
            <li class="dropdown">
                <a href="#kanbanMenu" class="dropdown-toggle" data-toggle="dropdown">Kanban <span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="#newKanban" class="mpkNew" title="New Kanban board" ng-click="newKanban()"><i class="glyphicon glyphicon-briefcase"></i> New</a>
                    </li>
                    <li><a href="#open" title="Open saved Kanban board" ng-click="openKanban()"><i class="glyphicon glyphicon-folder-open"></i> Open <small class="shortcut pull-right">ctrl-shift-o</small></a>
                    </li>
                    <li><a href="#delete" title="Downloadelete Kanban from local storage" ng-click="delete()"><i class="glyphicon glyphicon-remove-sign"></i> Delete</a></li>
                    <li><a href="#theme" title="Select Theme for the Kanban board" ng-click="selectTheme()"><i class="glyphicon glyphicon-picture"></i> Themes</a></li>
                    <li role="presentation" class="divider"></li>
                    <li><a href="#help" title="Help" ng-click="help()"><i class="glyphicon glyphicon-question-sign"></i> Help <small class="shortcut pull-right">ctrl-shift-h</small></a></li>
                </ul>
            </li>
        </ul>
          </li>
        </ul>
        <div id="info" class="nav pull-right" ng-show="showInfo">
          <span id="error" class="error" ng-show="showError"><a href="#close" ng-click="showInfo=false;showError=false;errorMessage=''">{{errorMessage}}</a></span>
          <span id="message" class="">{{infoMessage}}</span>
          <span id="spinner" class="pull-right" spin="spinConfig" spin-if="showSpinner"></span>
        </div>
      </div>
    </div>
  </div>
</header>

<div class="container-fluid" id="kanban" ng-controller="KanbanController">
    <div ng-model="kanban">
        <div id="columns" class="row">
            <div class="col-md-{{12/kanban.numberOfColumns}}" ng-repeat="column in kanban.columns" data-columnindex="{{$index}}" id="column{{$index}}">
                <div class="column">
                  <div class="columnHeader">
                      <a href="#addCard" title="Add card to column" class="pull-right" ng-click="addNewCard(column)"><i class="glyphicon glyphicon-plus"></i></a>
                      <a href="#changeColumnName" title="Change column name" ng-click="editing = true" ng-model="column" ng-hide="editing"><i class="glyphicon glyphicon-tasks"></i></a>
                      <span ng-hide="editing">{{column.name}} ({{column.cards.length}})</span>
                      <form ng-show="editing" ng-submit="editing = false">
                          <div class="input-group">
                              <span class="input-group-addon"><a href="#cancel" ng-click="editing = false"><i class="glyphicon glyphicon-tasks"></i></a></span>
                              <input class="form-control" type="text" ng-model="column.name" value="{{column.name}}" required="" focus-me="editing">
                          </div>
                      </form>
                  </div>
                  <ul class="cards" ui-sortable="{connectWith: '#kanban ul.cards'}" sortable="" ng-model="column.cards" style="{{minHeightOfColumn}}">
                      <li class="card" ng-repeat="card in column.cards" style="background-color: #{{colorFor(card)}}">
                          <a href="#deleteCard" class="pull-right" ng-click="delete(card, column)"><i class="glyphicon glyphicon-remove"></i></a>
                          <a ng-click="openCardDetails(card)"><span tooltip-popup-delay="2000" tooltip="{{details(card)}}">{{card.name}}</span></a>
                      </li>
                  </ul>
              </div>
            </div>
        </div>
    </div>
    <section id="kanbanOperations">
        <!-- this one is for the new card -->
        <script type="text/ng-template" id="NewKanbanModal.html">
            <form class="noMargin" ng-submit="createNew('#newKanban')" name="newKanbanForm">
                <div class="modal-header">
                  <button type="button" class="close" ng-click="closeNewKanban()">&times;</button>
                  <h4 class="modal-title" id="myModalLabel">New Kanban board</h4>
                </div>
                <div class="modal-body">
                  <div class="form-group">
                      <label class="control-label" for="kanbanNameFormField">Kanban name</label>
                      <div>
                        <input type="text" id="kanbanNameFormField" placeholder="Kanban name" class="form-control" ng-model="kanbanName" required focus-me />
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="control-label" for="numberOfColumnsField">Number of columns</label>
                      <div>
                        <select id="numberOfColumnsField" ng-model="numberOfColumns" class="form-control">
                          <option>2</option>
                          <option selected="selected">3</option>
                          <option>4</option>
                          <option>6</option>
                        </select>
                      </div>
                    </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default" ng-click="closeNewKanban()">Close</button>
                  <button type="submit" class="btn btn-primary" >Create new</button>
                </div>
            </form>
          </script> 
           
          <script type="text/ng-template" id="OpenKanban.html">
            <form ng-submit="open()" name="openKanbanForm" class="noMargin">
              <div class="modal-header">
                <button type="button" class="close" ng-click="close()">&times;</button>
                <h4 class="modal-title" id="openKanbanLabel">Open another Kanban</h4>
              </div>
              <div class="modal-body">
                    <div class="form-group">
                        <label class="control-label" for="kanbanNameFormField">Kanban name</label>
                        <div>
                            <select name="selectedToOpen" ng-model="selectedToOpen" class="form-control" required ng-options="name for name in allKanbans" focus-me="selectedToOpen">
                            </select>
                       </div>
                    </div>
              </div>
              <div class="modal-footer">
                <button class="btn btn-default" type="button" ng-click="close()">Close</button>
                <button class="btn btn-primary" type="submit">Open</button>
              </div>
            </form>
          </script>
            <script type="text/ng-template" id="NewKanbanCard.html">
              <form ng-submit="addNewCard()" class="noMargin" name="newCardForm">
                  <div class="modal-header">
                    <button type="button" class="close" ng-click="close()">&times;</button>
                    <h4 class="modal-title" ng-model="kanbanColumnName">New card for column '{{kanbanColumnName}}'</h4>
                  </div>
                  <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label" for="newCardTitle">Kanban card title</label>
                            <div>
                              <input type="text" id="newCardTitle" placeholder="Title on a card" ng-model="title" required focus-me class="cardInputs form-control"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="newCardDetails">More details <small>(optional)</small></label>
                            <div>
                                <textarea id="newCardDetails" ng-model="details" class="cardInputs form-control" rows="7" >
                                </textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Card color</label>
                        </div>
                        <div class="form-group">
                            <color-selector options="colorOptions" ng-model="cardColor" prefix="newCardColor" class="colorSelector" show-hex-code="true"/>
                        </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" ng-click="close()">Close</button>
                    <button type="submit" class="btn btn-primary">Add</button>
                  </div>
              </form>
        </script>
        <script type="text/ng-template" id="OpenCard.html">
              <form ng-submit="update()" class="noMargin" name="cardDetails">
                  <div class="modal-header">
                    <button type="button" class="close" ng-click="close()">&times;</button>
                    <h4 class="modal-title">Card details</h4>
                  </div>
                  <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label" for="cardTitle">Kanban card title</label>
                            <div class="controls">
                              <ng-form ng-submit="editingTitle = false">
                              <div>
                                <input name="cardTitle" type="text" id="cardTitle" placeholder="Title on a card" ng-model="name" required class="cardInputs" ng-disabled="!editingTitle" focus-me />
                                <a href="#editTitle" title="Edit card title" ng-click="editingTitle = true" ng-hide="editingTitle" class="btn pull-right"><i class="glyphicon glyphicon-pencil"></i></a>                              
                                <a href="#editTitle" title="OK" ng-click="editingTitle = false" ng-hide="!editingTitle" class="btn pull-right"><i class="glyphicon glyphicon-ok"></i></a>
                              </ng-form>
                              </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="cardTitle">Details</label>

                            <div class="controls clearfix">
                                <textarea id="details" ng-model="details" class="cardInputs" rows="7" ng-show="editDetails">
                                </textarea>
                                <div class="cardDetails cardInputs pull-left" ng-show="!editDetails" ng-bind-html="details|linky|cardDetails"></div>

                                <a href="#editDetails" title="Edit card title" ng-click="editDetails = true" ng-hide="editDetails" class="btn pull-right"><i class="glyphicon glyphicon-pencil"></i></a>                              
                                <a href="#editDetails" title="OK" ng-click="editDetails = false" ng-hide="!editDetails" class="btn pull-right"><i class="glyphicon glyphicon-ok"></i></a>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Card color</label>
                        </div>
                        <div class="form-group">
                            <color-selector options="colorOptions" ng-model="cardColor" prefix="editCardColor" class="colorSelector" show-hex-code="true" />
                        </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" ng-click="close()">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                  </div>
              </form>
        </script>

        <script type="text/ng-template" id="SelectTheme.html">
            <form ng-submit="switchTheme()" name="selectTheme" class="noMargin">
              <div class="modal-header">
                <button type="button" class="close" ng-click="close()">&times;</button>
                <h4 class="modal-title" id="openKanbanLabel">Choose Kanban Theme</h4>
              </div>
              <div class="modal-body">
                <div class="form-group">
                    <label class="control-label" for="kanbanTheme">Themes to choose from</label>
                </div>
                <div class="row">
                    <div class="col-md-5">
                      <select class="form-control" name="selectedToOpen" ng-model="model.selectedTheme" required ng-options="t.css as t.name for t in model.themes" id="kanbanTheme">
                      </select>
                    </div>
                    <div class="col-md-5">
                      <img src="img/themes/{{model.selectedTheme}}.jpg" width="250" class="thumbnail" style="border: 1px solid black"/>
                   </div>
                </div>
              </div>
              <div class="modal-footer">
                <button class="btn btn-default" type="button" ng-click="close()">Close</button>
                <button class="btn btn-primary" type="submit" ng-click="switchTheme()">Switch</button>
              </div>
            </form>
          </script>

          <script type="text/ng-template" id="SetupCloudModal.html">
            <form ng-submit="saveSettings()" name="cloudSettings" class="noMargin">
              <div class="modal-header">
                <button type="button" class="close" ng-click="close()">&times;</button>
                <h4 class="modal-title">Cloud Setup</h4>
              </div>
              <div class="modal-body">
                <div class="alert alert-danger" ng-show="model.showConfigurationError">
                  <p>In order to use Cloud Upload and Download functionality, you need to generate Kanban Key, a unique identifier that will be used to upload and download your Kanban. You can to that at My Personal Kanban web service <a href="http://my-personal-kanban.appspot.com" target="blank">http://my-personal-kanban.appspot.com</a></p>
                </div>
                <div class="form-group">
                    <label class="control-label" for="kanbanKey">Kanban key:</label>
                    <div>
                      <input type="text" name="kanbanKey" id="kanbanKey" ng-model="model.kanbanKey" required class="kanbanKey form-control" placeholder="Cloud Kanban key" valid-key />
                      <span class="text-danger" ng-show="cloudSettings.kanbanKey.$error.validKey">Provided key is invalid</span>
                      <span class="text-danger" ng-show="cloudSettings.kanbanKey.$error.validKeyUnableToVerify">Unable to validate key. You might not be connected to the Internet or unable to access <a href="http://my-personal-kanban.appspot.com" target="blank">http://my-personal-kanban.appspot.com</a></span>

                   </div>
                </div>
                <div class="alert alert-info" ng-hide="model.showConfigurationError">
                  <p>If you need to retrieve your kanban key or generate a new one go to <a href="http://my-personal-kanban.appspot.com" target="blank">http://my-personal-kanban.appspot.com</a></p>
                </div>
                <div class="form-group">
                    <label class="control-label" for="cloudEncryptionKey">Cloud encryption key:</label>
                    <div>
                      <input type="text" name="cloudEncryptionKey" id="cloudEncryptionKey" class="kanbanKey form-control" ng-model="model.encryptionKey"/>
                    </div>
                </div>
                <div class="alert alert-info">
                  <p>This key will be used to Encrypt your Kanban when uploading into Cloud and Decrypt when downloading. It can be any number of characters.</p>
                  <p><small>If you make changes to this key, make sure to download latest Kanban from Cloud first and upload after.</small></p> 
                </div>
              </div>              
              <div class="modal-footer">
                <button class="btn btn-default" type="button" ng-click="close()">Close</button>
                <button class="btn btn-primary" type="submit" ng-disabled="cloudSettings.kanbanKey.$error.validKey || cloudSettings.kanbanKey.$error.validKeyUnableToVerify">Save settings</button>
              </div>
            </form>
          </script>

          <script type="text/ng-template" id="HelpModal.html">
              <div class="modal-header">
                <button type="button" class="close" ng-click="close()">&times;</button>
                <h4 class="modal-title">Help</h4>
              </div>
              <div class="modal-body">
                <p><span class="version">My Personal Kanban Version: 0.4.0</span></p>
                <p>
                  <strong>Personal Kanban</strong><br />
                  <small>You can create <strong>new Kanban</strong> by selecting <strong>Kanban -> New</strong> from the Kanban menu. You can give it a name and select number of columns.<br/>
                  Once Kanban is created you can <strong>rename</strong> it by <strong>clicking it's title</strong> in the top bar. You can add new cards to the columns by pressing <strong>&plus;</strong> in the top right of the column. <br />
                    You can give each <strong>column name</strong> by clicking on icon next to it's name. <strong>(Number)</strong> next to column name indicates number of cards in the column.<br /><br />
                    <strong>Cards are created</strong> with <strong>title</strong> and possible longer <strong>description</strong>. You can also <strong>select different colour</strong> for the card if you would like to categorize them somehow. Description will <strong>keep the new line formating</strong> and add <strong>clickable links</strong> if you include any. <br />
                    You can <strong>open Card details</strong> by clicking on <strong>card title</strong>.You can <strong>edit Card details</strong> once opened.<br />
                    You can <strong>drag card between columns</strong> and move them <strong>up and down</strong> column list.<br /><br />
                    You can have <strong>multiple Kanbans</strong> and open/switch them from the <strong>Kanban -> Open</strong> menu or by <strong>pressing <abbr title="Ctrl-Shift-o when browser window is selected">Ctrl-Shift-o</abbr></strong> on keyboard. <br /> 
                      You can <strong>select different style</strong> of Kanban board from <strong>Kanban -> Theme</strong> menu.<br />
                      You can <strong>delete</strong> entire Kanban by selecting <strong>Kanban -> Delete</strong> from the menu.<br />
                      Your Kanbans are automatically <strong>saved</strong> in your <strong>browser storage</strong>. 
                  </small>
                </p>
                <p>
                  <strong>Cloud</strong><br />
                  <small>
                    You can use <strong>My Personal Kanban Cloud</strong> service that enables <strong>Upload and Download</strong> from Cloud. This service is offered free of charge. <br />
                    You will need to create your <strong>cloud Kanban key</strong>. Follow instructions on <strong>Cloud -> Setup</strong>.<br />
                    Your Kanban in the Cloud <strong>is stored Encrypted</strong>, that's why you should setup <strong>Encryption Key</strong>. The Encryption algorithm is <a href="http://en.wikipedia.org/wiki/Rabbit_(cipher)" target="blank" title="Rabbit encryption algorithm">Rabbit</a>.<br />
                    You can <strong>upload Kanban</strong> by selecting <strong>Cloud -> Upload</strong> from the menu <br />.
                    You can <strong>download Kanban</strong> by selecting <strong>Cloud -> Download</strong> from the menu.
                  </small>
                </p>
              </div>              
              <div class="modal-footer">
                <small class="pull-left">You can open this help anytime by pressing <abbr title="Ctrl-Shift-h when browser window is selected">Ctrl-Shift-h</abbr> on keyboard.</small>
                <button class="btn btn-default" type="button" ng-click="close()">Close</button>
              </div>
          </script>
    </section>
</div>
<footer>

</footer>
        <script src="<?php echo Yii::app()->theme->baseUrl;?>/kanban/bower_components/jquery/jquery.min.js"></script>
        <script src="<?php echo Yii::app()->theme->baseUrl;?>/kanban/bower_components/angular/angular.min.js"></script>
        <script src="<?php echo Yii::app()->theme->baseUrl;?>/kanban/bower_components/angular-sanitize/angular-sanitize.min.js"></script>
        <script src="<?php echo Yii::app()->theme->baseUrl;?>/kanban/bower_components/angular-ui-bootstrap-bower/ui-bootstrap.min.js"></script>
        <script src="<?php echo Yii::app()->theme->baseUrl;?>/kanban/bower_components/angular-ui-bootstrap-bower/ui-bootstrap-tpls.min.js"></script>
        <script src="<?php echo Yii::app()->theme->baseUrl;?>/kanban/bower_components/jquery-ui/ui/minified/jquery-ui.min.js"></script>
        <script src="<?php echo Yii::app()->theme->baseUrl;?>/kanban/bower_components/angular-ui-utils/ui-utils.min.js"></script>
        <script src="<?php echo Yii::app()->theme->baseUrl;?>/kanban/bower_components/spinjs/spin.js"></script>
        <script src="<?php echo Yii::app()->theme->baseUrl;?>/kanban/scripts/5ebce75f.themes.js"></script>
        <script src="<?php echo Yii::app()->theme->baseUrl;?>/kanban/scripts/cbacee66.scripts.js"></script>
</body>
</html>