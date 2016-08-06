<?php

/** * ***********************************************************
 *  File Name : CHelper.php
 *  File Description: CHelper class file to shorten the yii common useful functions .
 *  Author: Benchmark, 
 *  Created Date: 17 Feb 2014
 *  Created By: Anand Rathi & Rakesh Jaware.
 * ************************************************************* */
/*      Note::  Please access the functions defined in this class as given below
 *              Eg: For getting the root path to the webapp
 *                  $rootPath = Helper::webapp()
 *                  i.e CHelper::functionName();
 *                  
 *              Eg: for creating the text area using function declared in this class use following syntax
 *                  echo CHelper::textArea("name", 'Default Value');
 *                  i.e  CHelper::functionName()
 * 
 */
class CHelper {

    public static function getReturnUrl($model_name = '') {
        $return_url = Yii::app()->user->getState('return_url');
        if (!empty($model_name))
            return ( isset($return_url[$model_name]) ) ? $return_url[$model_name] : '';
        else
            return Yii::app()->request->urlReferrer;
    }

    /* ---------------------------------------------------------------- */
    /* ---------------------------yii Shorten-------------------------- */
    /* ---------------------------------------------------------------- */
    /* Function Name:       webapp()
     * Description:         This function returns the instance of the web application i.e Yii::app();
     * Parameters:          No Parameters
     */

    public static function webapp() {
        return Yii::app();
    }

    /** Function Name :      baseUrl()
     *  Description :        This function returns the base url of the web application's root folder
     *  Parameters :         No parameters
     */
    public static function baseUrl($absolute = false) {
        //return Yii::app()->request->baseUrl;
        return Yii::app()->getBaseUrl($absolute);
    }

    /** Function Name :      basePath()
     *  Description :        This function returns the base path of the web application's root folder
     *  Parameters :         No parameters
     */
    public static function basePath() {
        return Yii::app()->basePath;
    }

    /** Function Name :      userFilesUrl()
     *  Description :        This function returns the url of the the USER FILES folder
     *  Parameters :         No parameters
     */
    public static function userFilesUrl($create_dir = false) {
        return ( $create_dir ) ? 'user_files/' : Yii::app()->request->baseUrl . "/user_files";
    }

    /** Function Name :      projectUrl()
     *  Description :        This function returns the url upto project name
     *  Parameters :         No parameters
     */
    public static function projectUrl() {
        if ($_SERVER['SERVER_PORT'] == "443")
            return "https://" . $_SERVER['HTTP_HOST'] . Yii::app()->request->baseUrl;
        else
            return "http://" . $_SERVER['HTTP_HOST'] . Yii::app()->request->baseUrl;
    }

    /** Function Name :      homeUrl()
     *  Description :        This function returns the home url of the web application's root folder
     *  Parameters :         No parameters
     */
    public static function homeUrl() {
        $homeurl = Yii::app()->request->baseUrl . "/index.php";
        return $homeurl;
    }

    /** Function Name: registerCssFile($url, $media='')
     *  Description:   Registers a CSS file
     *  Parameters:
     *                1) $url=>         string URL of the CSS file
     *                2) $media=>       string $media media that the CSS file should be applied to.
     *                                  If empty, it means all media types.
     */
    public static function registerCssFile($url, $media = '') {
        return Yii::app()->clientScript->registerCssFile($url, $media);
    }

    /**  Function Name: registerCss($id, $css, $media='')
     *   Description:   Registers a piece of CSS code.
     *   Parameters:    
     *                  1)$id =>    string ID that uniquely identifies this piece of CSS code
     *                  2)$css =>   string the CSS code
     *                  3)$media => string $media media that the CSS code should be applied to. 
     *                              If empty, it means all media types.
     */
    public static function registerCss($id, $css, $media = '') {
        return Yii::app()->clientScript->registerCss($id, $css, $media);
    }

    /**  Function Name: registerScriptFile($url, $position=CClientScript::POS_HEAD)
     *   Description:   Registers a javascript file.
     *   Parameters:    
     *                  1)$url =>   URL of the javascript file
     *                  2)$position => the position of the JavaScript code. Valid values include the following:
     * 
     *                               A)  CClientScript::POS_HEAD  : the script is inserted in the head section right
     *                                                              before the title element.
     *                               B)  CClientScript::POS_BEGIN : the script is inserted at the beginning of the 
     *                                                              body section.
     *                               C)  CClientScript::POS_END   : the script is inserted at the end of the body
     *                                                               section.
     */
    public static function registerScriptFile($url, $position = CClientScript::POS_HEAD) {
        return Yii::app()->clientScript->registerScriptFile($url, $position);
    }

    /**  Function Name: registerScript($id,$script,$position=CClientScript::POS_READY)
     *   Description:   Registers a piece of javascript code.
     *   Parameters:    
     *                  1)$id => string ID that uniquely identifies this piece of JavaScript code.
     *                  2)$script => string the javascript code
     *                  3)$position => integer the position of the JavaScript code. Valid values include the following:
      A)   CClientScript::POS_HEAD : the script is inserted in the head section
     *                                                                right before the title element.
     *                                 B)   CClientScript::POS_BEGIN : the script is inserted at the beginning of
     *                                                                 the body section.
     * 
     *                                 C)   CClientScript::POS_END : the script is inserted at the end of the body 
     *                                                               section.
     *                                 D)   CClientScript::POS_LOAD : the script is inserted in the window.onload()
     *                                                                function.
     *                                 E)   CClientScript::POS_READY : the script is inserted in the jQuery's 
     *                                                                 ready function.
     */
    public static function registerScript($id, $script, $position = CClientScript::POS_READY) {
        return Yii::app()->clientScript->registerScript($id, $script, $position);
    }

    /*   Function Name: clientScriptMap()
     *   Description:   Client Script mapping on ajax call
     *   Parameters:    No parameters 
      A] $scripts = It would be array or single string
      A] $inaclude = default true
     */

    public static function clientScriptMap($scripts, $inaclude = true) {
        if (is_array($scripts)) {
            foreach ($scripts as $script) {
                Yii::app()->clientScript->scriptMap[$script] = $inaclude;
            }
        } else {
            Yii::app()->clientScript->scriptMap[$scripts] = $inaclude;
        }
    }

    /*   Function Name: user()
     *   Description:   Returns the Information of the currently logged in user
     *   Parameters:    
     * 
     */

    public static function user() {
        return Yii::app()->getUser();
    }

    /** Function Name:  createUrl($route, $params=array(), $ampersand='&')
     *  Description:    As mentioned below
     *                  This will upon creation of the url shorten, it will elemenate the line below.
     *                  1. $this->createUrl() 
     *                  2. Yii::app()->controller->createUrl()
     *                  3. Yii::app()->createUrl()
     * 
     *                  Creates a relative URL for the specified action defined in this controller.
     *  Parameters:     
     *                  A)  $route(string) =>   the URL route. This should be in the format of 'ControllerID/ActionID'.
     *                                          If the ControllerID is not present, the current controller ID will be prefixed to the route.
     *                                          If the route is empty, it is assumed to be the current action.
     *                                          Since version 1.0.3, if the controller belongs to a module, the {@link CWebModule::getId module ID}
     *                                          will be prefixed to the route. (If you do not want the module ID prefix, the route should start with a slash '/'.)
     *                  B)  $params(array) =>   array $params additional GET parameters (name=>value). Both the name and value will be URL-encoded.
     *                                          If the name is '#', the corresponding value will be treated as an anchor
     *                                          and will be appended at the end of the URL. This anchor feature has been available since version 1.0.1.
     *                  C)  $ampersand(string) =>  the token separating name-value pairs in the URL.
     *  @return string the constructed URL               
     */
    public static function createUrl($route, $params = array(), $ampersand = '&') {
        return Yii::app()->controller->createUrl($route, $params, $ampersand);
    }

    /** Function Name:  registerMetaTag($content, $name=NULL, $httpEquiv=NULL, $options=array())

     *  Description:    To register a MetaTag.
     *                  Registers a meta tag that will be inserted in the head section (right before the title
     *                   element) of the resulting page.
     *  Parameters:     
     *                  A)  $content(string) =>     content attribute of the meta tag
     *                  B)  $name(string)   =>      Name attribute of the meta tag. If null, the attribute will 
     *                                              not be generated.
     *                  C)  $httpEquiv(string)  =>  http-equiv attribute of the meta tag. If null, the attribute 
     *                                              will not be generated
     *                  D)  $options(array)    =>   other options in name-value pairs (e.g. 'scheme', 'lang')
     * 
     * @return CClientScript the CClientScript object itself (to support method chaining, available since version 1.1.5).
     * @since 1.0.1          
     */
    public static function registerMetaTag($content, $name = NULL, $httpEquiv = NULL, $options = array()) {
        return Yii::app()->clientScript->registerMetaTag($content, $name, $httpEquiv, $options);
    }

    /** Function Name:
     * A function that will elemenate the long lines of coding to get the parameter.
     * Yii::app()->params['Parameter']
     * 
     * If there will be a nested parameter we defined, we can pull the data by calling this function, and
     * used the character "." (period), example below
     * 
     * 1. params('data')
     * 2. params('data.mysample');
     * 
     * This is to pull upto 10 nested parameters.
     * @param parameter $attribute to pull
     * @return value of params
     */
    function params($attribute) {
        $s = explode(".", $attribute);
        switch (count($s)) {
            case 1:
                return webapp()->params[$attribute];
            case 2:
                return webapp()->params[$s[0]][$s[1]];
            case 3:
                return webapp()->params[$s[0]][$s[1]][$s[2]];
            case 4:
                return webapp()->params[$s[0]][$s[1]][$s[2]][$s[3]];
            case 5:
                return webapp()->params[$s[0]][$s[1]][$s[2]][$s[3]][$s[4]];
            case 6:
                return webapp()->params[$s[0]][$s[1]][$s[2]][$s[3]][$s[4]][$s[5]];
            case 7:
                return webapp()->params[$s[0]][$s[1]][$s[2]][$s[3]][$s[4]][$s[5]][$s[6]];
            case 8:
                return webapp()->params[$s[0]][$s[1]][$s[2]][$s[3]][$s[4]][$s[5]][$s[6]][$s[7]];
            case 9:
                return webapp()->params[$s[0]][$s[1]][$s[2]][$s[3]][$s[4]][$s[5]][$s[6]][$s[7]][$s[8]];
            case 10:
                return webapp()->params[$s[0]][$s[1]][$s[2]][$s[3]][$s[4]][$s[5]][$s[6]][$s[7]][$s[8]][$s[9]];
        }
    }

    /* ---------------------------------------------------------------- */
    /* --------------------------HTML Shorten-------------------------- */
    /* ---------------------------------------------------------------- */

    /**
     * Shorten all the codes we upon generating an HTML tag by calling the class CHtml::*
     * Function Name:   encode($text)
     * Description:     
     *                  Encodes special characters into HTML entities.
     *                  The {@link CApplication::charset application charset} will be used for encoding.
     * Parameters:      
     *                  A)  $text(string) =>    data to be encoded
     * @return string the encoded data
     * @see http://www.php.net/manual/en/function.htmlspecialchars.php
     */
    function encode($text) {
        return CHtml::encode($text);
    }

    /**     Function Name:   metaTag($content,$name=null,$httpEquiv=null,$options=array())
     *      Description:     Generates a meta tag that can be inserted in the head section of HTML page.
     *      Parameters:     
     *                       A) $content(string)    => content attribute of the meta tag
     *                       B) $name(string)       => name attribute of the meta tag. If null, the attribute will 
     *                                                 not be generated.
     *                       C) $httpEquiv(string)  => http-equiv attribute of the meta tag. If null, the 
     *                                                 attribute will not be generated
     *                       D) $options(array)     => other options in name-value pairs (e.g. 'scheme', 'lang')
     * @return string the generated meta tag
     * @since 1.0.1
     */
    function metaTag($content, $name = null, $httpEquiv = null, $options = array()) {
        return CHtml::metaTag($content, $name, $httpEquiv, $options);
    }

    /** Function Name:  css($text,$media='')
     *  Description:    Encloses the given CSS content with a CSS tag.
     *  Parameters:     
     *                  A)  $text(string)     =>  the CSS content
     *                  B)  $media(string)    =>  the media that this CSS should apply to.
     * 
     * @return string the CSS properly enclosed
     */
    function css($text, $media = '') {
        return CHtml::css($text, $media);
    }

    /** Function Name:  cssFile($url,$media='')
     *  Description:    Links to the specified CSS file.
     *  Parameters:     
     *                  A)  $url(string)    =>  string $url the CSS URL
     *                  B)  $media(string)  =>  the media that this CSS should apply to.
     * 
     * @return string the CSS link.
     */
    function cssFile($url, $media = '') {
        return CHtml::cssFile($url, $media);
    }

    /**    Function Name:    script($text)
     *     Description:      Encloses the given JavaScript within a script tag.
     *     Parameters:
     *                       A) $text(string)   =>   the JavaScript to be enclosed
     * 
     * @return string the enclosed JavaScript
     */
    function script($text) {
        return CHtml::script($text);
    }

    /**     Function Name:  scriptFile($url)
     *      Description:    Includes a JavaScript file.
     *      Parameters:
     *                      A)  $url(string)    =>  URL for the JavaScript file
     * @return string the JavaScript file tag
     */
    function scriptFile($url) {
        return CHtml::scriptFile($url);
    }

    /**     Function Name:  hyperlink($text,$url='#',$htmlOptions=array())
     *      Description:    Generates a hyperlink tag.
     *      Parameters:
     *                      A)  $text(string)       =>  link body. It will NOT be HTML-encoded. Therefore you can pass
     *                                                  in HTML code such as an image tag.
     *                      B)  $url(string)        =>  a URL or an action route that can be used to create a URL.
     *                      C)  $htmlOptions(array) =>  additional HTML attributes. Besides normal HTML attributes,
     *                                                  a few special attributes are also recognized (see 
     * @return string the generated hyperlink
     * @see normalizeUrl
     * @see clientChange
     */
    function hyperlink($text, $url = '#', $htmlOptions = array()) {
        return CHtml::link($text, $url, $htmlOptions);
    }

    /**     Function Name:     image($src,$alt='',$htmlOptions=array())
     *      Description:       Generates an image tag.
     *      Parameters:     .
     *                         A) $src(string)          =>  the image URL
     *                         B) $alt(string)          =>  the alternative text display
     *                         C) $htmlOptions(array)   =>  additional HTML attributes (see {@link tag}).
     * @return string the generated image tag
     */
    function image($src, $alt = '', $htmlOptions = array()) {
        return CHtml::image($src, $alt, $htmlOptions);
    }

    /**     Function Name:     button($label='button',$htmlOptions=array())
     *      Description:       Generates a button.
     *      Parameters:     .
     *                         A) $label(string)        =>  the button label
     *                         B) $htmlOptions(array)   =>  additional HTML attributes. Besides normal HTML 
     *                                                      attributes, a few special attributes are also recognized 
     *                                                      (see {@link clientChange} and {@link tag} for more details.)
     * @return string the generated button tag
     * @see clientChange
     */
    function button($label = 'button', $htmlOptions = array()) {
        return CHtml::button($label, $htmlOptions);
    }

    /**     Function Name:     submitButton($label='submit',$htmlOptions=array())
     *      Description:       Generates a submit button.
     *      Parameters:     .
     *                         A) $label(string)        =>  the button label
     *                         B) $htmlOptions(array)   =>  additional HTML attributes. Besides normal HTML 
     *                                                      attributes, a few special attributes are also recognized 
     *                                                      (see {@link clientChange} and {@link tag} for more details.) 
     * @return string the generated button tag
     * @see clientChange
     */
    function submitButton($label = 'submit', $htmlOptions = array()) {
        return CHtml::submitButton($label, $htmlOptions);
    }

    /**     Function Name:     label($label,$for,$htmlOptions=array())
     *      Description:       Generates a label tag.
     *      Parameters:     .
     *                         A) $label(string)        =>  $label label text. Note, you should HTML-encode the text if needed.
     *                         B) $for(string)          =>  the ID of the HTML element that this label is associated with.
     *                         C) $htmlOptions(array)   =>  additional HTML attributes. Besides normal HTML 
     *                                                      attributes, a few special attributes are also recognized 
     *                                                      (see {@link clientChange} and {@link tag} for more details.) 
     *                                                      If this is false, the 'for' attribute for the label tag will not be rendered (since version 1.0.11).
     * @return string the generated label tag
     */
    function label($label, $for, $htmlOptions = array()) {
        return CHtml::label($label, $for, $htmlOptions);
    }

    /**     Function Name:     textField($name,$value='',$htmlOptions=array())
     *      Description:       Generates a text field input.
     *      Parameters:     .
     *                         A) $name(string)         =>  the input name
     *                         B) $value(string)        =>  the input value
     *                         C) $htmlOptions(array)   =>  additional HTML attributes. Besides normal HTML 
     *                                                      attributes, a few special attributes are also recognized 
     * @return string the generated input field
     *
     */
    function textField($name, $value = '', $htmlOptions = array()) {
        return CHtml::textField($name, $value, $htmlOptions);
    }

    /**      Function Name:     hiddenField($name,$value='',$htmlOptions=array())
     *       Description:       Generates a hidden input.
     *       Parameters:     
     *                         A) $name(string)         =>  the input name
     *                         B) $value(string)        =>  the input value
     *                         C) $htmlOptions(array)   =>  additional HTML attributes. Besides normal HTML 
     *                                                      attributes, a few special attributes are also recognized 
     * @return string the generated input field
     * @see inputField
     */
    function hiddenField($name, $value = '', $htmlOptions = array()) {
        return CHtml::hiddenField($name, $value, $htmlOptions);
    }

    /**     Function Name:     passwordField($name,$value='',$htmlOptions=array())
     *      Description:       Generates a password field input.
     *      Parameters:     
     *                         A) $name(string)         =>  the input name
     *                         B) $value(string)        =>  the input value
     *                         C) $htmlOptions(array)   =>  additional HTML attributes. Besides normal HTML 
     *                                                      attributes, a few special attributes are also recognized 
     * @return string the generated input field
     * @see clientChange
     * @see inputField
     */
    function passwordField($name, $value = '', $htmlOptions = array()) {
        return CHtml::passwordField($name, $value, $htmlOptions);
    }

    /**      Function Name:     textArea($name,$value='',$htmlOptions=array())
     *       Description:       Generates a text area input.
     *       Parameters:     
     *                         A) $name(string)         =>  the input name
     *                         B) $value(string)        =>  the input value
     *                         C) $htmlOptions(array)   =>  additional HTML attributes. Besides normal HTML 
     *                                                      attributes, a few special attributes are also recognized 
     * @return string the generated text area
     * @see clientChange
     * @see inputField
     */
    function textArea($name, $value = '', $htmlOptions = array()) {
        return CHtml::textArea($name, $value, $htmlOptions);
    }

    /**      Function Name:     radioButton($name,$checked=false,$htmlOptions=array())
     *       Description:       Generates a radio button.
     *       Parameters:     
     *                         A) $name(string)         =>  the input name
     *                         B) $checked(boolean)     =>  whether the radio button is checked
     *                         C) $htmlOptions(array)   =>  additional HTML attributes. Besides normal HTML 
     *                                                      attributes, a few special attributes are also recognized 
     *                                                      Since version 1.1.2, a special option named 'uncheckValue' 
     *                                                      is available that can be used to specify
     *                                                      the value returned when the radio button is not checked. 
     *                                                      When set, a hidden field is rendered so that
     *                                                      when the radio button is not checked, we can still 
     *                                                      obtain the posted uncheck value.
     *                                                      If 'uncheckValue' is not set or set to NULL, the hidden 
     *                                                      field will not be rendered.
     * @return string the generated radio button
     */
    function radioButton($name, $checked = false, $htmlOptions = array()) {
        return CHtml::radioButton($name, $checked, $htmlOptions);
    }

    /**      Function Name:      checkBox($name,$checked=false,$htmlOptions=array())
     *       Description:       Generates a check box.
     *       Parameters:     
     *                         A) $name(string)         =>  the input name
     *                         B) $checked(boolean)     =>  whether the radio button is checked
     *                         C) $htmlOptions(array)   =>  additional HTML attributes. Besides normal HTML 
     *                                                      attributes, a few special attributes are also recognized 
     *                                                      Since version 1.1.2, a special option named 'uncheckValue'
     *                                                      is available that can be used to specify
     *                                                      the value returned when the checkbox is not checked. 
     *                                                      When set, a hidden field is rendered so that
     *                                                      when the checkbox is not checked, we can still obtain
     *                                                      the posted uncheck value.
     *                                                      If 'uncheckValue' is not set or set to NULL, the hidden 
     *                                                      field will not be rendered.
     * @return string the generated check box
     * @see clientChange
     * @see inputField
     */
    function checkBox($name, $checked = false, $htmlOptions = array()) {
        return CHtml::checkBox($name, $checked, $htmlOptions);
    }

    function dropDownList($name, $select, $data, $htmlOptions = array()) {
        return CHtml::dropDownList($name, $select, $data, $htmlOptions);
    }

    /**      Function Name:      setFlash($key, $value, $defaultValue=null)
     *       Description:        Sets/Stores a flash message.
     *       Parameters:     
     *                           A) $key(string)            =>  key identifying the flash message
     *                           B) $value(mixed)           =>  flash message
     *                           C) $defaultValue(mixed)    =>  if this value is the same as the flash message,
     *                                                          the flash message will be removed. 
     *                                                          (Therefore, you can use setFlash('key',null) 
     *                                                          to remove a flash message.)
     */
    /* ---------------------------------------------------------------- */
    /* -----------------------Application Messages--------------------- */
    /* ---------------------------------------------------------------- */
    public static function setFlash($key, $value, $defaultValue = null) {
        Yii::app()->user->setFlash($key, $value, $defaultValue);
    }

    /**      Function Name:      getFlash($key, $defaultValue=null, $delete=true)
     *       Description:        Returns a flash message.
     *       Parameters:     
     *                           A) $key(string)            =>  key identifying the flash message
     *                           B) $defaultValue(mixed)    =>  value to be returned if the flash message is not available.
     *                           C) $delete(boolean)        =>  whether to delete this flash message after accessing it.
     * @return mixed the message message
     */
    public static function getFlash($key, $defaultValue = null, $delete = false) {
        return Yii::app()->user->getFlash($key, $defaultValue, $delete);
    }

    /**      Function Name:      hasFlash($key)
     *       Description:        Checks whether the flash message with given key exists or not
     *       Parameters:     
     *                           A) $key(string)     =>  key identifying the flash message
     *  @return boolean whether the specified flash message exists
     */
    public static function hasFlash($key) {
        return Yii::app()->user->hasFlash($key);
    }

    /**      Function Name:      setFlashSuccess($value, $defaultValue = null)
     *       Description:        sets the flash message for activity success
     *       Parameters:     
     *                           A) $value(string)     =>  flash message content
     *                           B) $defaultValue      =>  Default value of the message 
     */
    public static function setFlashSuccess($value, $defaultValue = null) {
        Yii::app()->user->setFlash('success', $value, $defaultValue);
    }

    /**      Function Name:      setFlashError($value, $defaultValue = null)
     *       Description:        sets the flash message for activity failure
     *       Parameters:     
     *                           A) $value(string)     =>  flash message content
     *                           B) $defaultValue      =>  Default value of the message 
     */
    public static function setFlashError($value, $defaultValue = null) {
        Yii::app()->user->setFlash('error', $value, $defaultValue);
    }

    /**      Function Name:      setFlashNotice($value, $defaultValue = null)
     *       Description:        sets the flash message To notice the user
     *       Parameters:     
     *                           A) $value(string)     =>  flash message content
     *                           B) $defaultValue      =>  Default value of the message 
     */
    public static function setFlashNotice($value, $defaultValue = null) {
        Yii::app()->user->setFlash('notice', $value, $defaultValue);
    }

    /**      Function Name:      hasCookie($name)
     *       Description:        Checks whether the cookie with the given name exists
     *       Parameters:     
     *                           A) $name(string)     =>  name of the cookie
     *  @return boolean whether the cookie with the given name exists
     */
    public static function hasCookie($name) {
        return !empty(Yii::app()->request->cookies[$name]->value);
    }

    /**      Function Name:      getCookie($name)
     *       Description:        returns the cookie value with the given name
     *       Parameters:     
     *                           A) $name(string)     =>  name of the cookie
     *  @return cookie value with the given name
     */
    public static function getCookie($name) {
        return Yii::app()->request->cookies[$name]->value;
    }

    /**     Function Name:      setCookie($name, $value)
     *       Description:        sets the cookie value
     *       Parameters:     
     *                           A) $name(string)     =>  name of the cookie
     *                           B) $value            =>  value to be set   
     *                           
     *  @return sets cookie value for given name
     */
    public static function setCookie($name, $value) {
        $cookie = new CHttpCookie($name, $value);
        $cookie->httpOnly = true;
        $cookie->secure = true;
        Yii::app()->request->cookies[$name] = $cookie;
    }

    /**     Function Name:      removeCookie($name)
     *       Description:        unset or remove the cookie value
     *       Parameters:     
     *                           A) $name(string)     =>  name of the cookie
     *  @return removes cookie value for given name
     */
    public static function removeCookie($name) {
        unset(Yii::app()->request->cookies[$name]);
    }

    /**     Function Name:      getCurrentThemePath()
     *       Description:        gives the root path to current applied theme
     *       Parameters:         No parameters                
     *  @return root path to current applied theme
     */
    public static function getCurrentThemePath() {
        return Yii::app()->theme->getBaseUrl();
    }

    /**     Function Name:      setSessionValue($key, $value)
     *       Description:        sets the value of the session variable having name $key with $value
     *       Parameters:         
     *                           A) $key(string)    =>  the id or name of the session variable
     *                           B) $value(string)  =>  the value to be set for the given session variable
     */
    public static function setSessionValue($key, $value) {
        Yii::app()->session[$key] = $value;
    }

    /**      Function Name:      getSessionValue($key)
     *       Description:        returns the value of the session variable having name as $key 
     *       Parameters:         
     *                           A) $key(string)    =>  the id or name of the session variable
     *      @return the value stored in session with $key
     */
    public static function getSessionValue($key) {
        Yii::app()->session['var'];
    }

    /* ---------------------------------------------------------------- */
    /* ------------------------Development----------------------------- */
    /* ---------------------------------------------------------------- */

    /**
     *  Function Name   :	debug ( for development )
     *  Description     :	to debug any variable
     *  Parameters	-
     * 	$variable   -	any variable
     * 	$die        -	default true
     */
    public static function debug($variable, $die = true) {
        echo "<pre>";
        echo "<div class='row' style='display:table;color:white;background:black'><div style='width: 50%; display: table-cell; vertical-align:top'>";
        echo "<h1 style='color:orange'>PRINT ARRAY</h1>";
        echo "<pre style='padding:10px 20px;border-right:1px solid white'>";
        print_r($variable);
        echo "</pre>";
        echo "</div>";
        echo "<div div style='width: 50%; display: table-cell; vertical-align:top'>";
        echo "<h1 style='color:orange'>VAR DUMP</h1>";
        echo "<pre style='padding:10px 20px;'>";
        var_dump($variable);
        echo "</pre>";
        echo "</div></div>";
        echo "</pre>";
        ( $die ) ? die() : '';
    }

    /**
     *  Function Name:	dump ( for development )
     *  Description:	to dump any variable
     *  Parameters	- ( optional )
     * 	$variable	-	any variable
     */
    public static function dump() {
        $args = func_get_args();
        foreach ($args as $k => $arg) {
            echo '<fieldset class="debug">
				<legend>' . ($k + 1) . '</legend>';
            CVarDumper::dump($arg, 10, true);
            echo '</fieldset>';
        }
    }

    /* ---------------------------------------------------------------- */
    /* ----------------------Extra Yii Function------------------------ */
    /* ---------------------------------------------------------------- */

    /**
     *  Function Name:	renderStaticContent
     *  Description:	to render static view
     *  Parameters	-
     * 		$view_path	-	path of view
     * 		$view_name	-	name of view
     *  @return the value mac address
     */
    public static function renderStaticContent($view_path, $view_name) {
        $path = Yii::getPathOfAlias($view_path) . '/' . $view_name . '.php';
        return file_get_contents($path);
    }

    /**
     *  Function Name:	getmacAddress()
     *  Description:	returns the mac address
     *  Parameters:		No parameters
     *  @return the value mac address
     *  @developer yogesh jadhav
     */
    public static function getmacAddress() {
        ob_start();     // Turn on output buffering
        system('ipconfig /all');   // Execute external program to display output
        $mycom = ob_get_contents(); // Capture the output into a variable
        ob_clean();     // Clean (erase) the output buffer

        $findme = "Physical";
        $pmac = strpos($mycom, $findme);  // Find the position of Physical text
        $mac = substr($mycom, ($pmac + 36), 17); // Get Physical Address

        return $mac;
    }

    /**
     *  Function Name:	getipAddress()
     *  Description:	returns the ip address of client machine 
     *  Parameters:		No parameters
     *  @return the value ip address
     *  @developer yogesh jadhav
     */
    public static function getipAddress() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {             // check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {   // to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**     Function Name:      getCapitalString()
     *       Description:         returns the string in capital letter
     *       Parameters:          string 
     *      @return the capital string
     *  @developer Anupam          
     */
    public static function getCapitalString($string) {
        return strtoupper($string);
    }

    /**    Function Name:      getTrimData()
     *      Description:        returns value with trim
     *      Parameters:         string , POST GET Data
     *      @return the trim data
     *      @developer Anupam
     */
    public static function getTrimData($name, $data = array()) {
        if (isset($data[$name]) && !empty($data[$name]))
            return trim($data[$name]);
        else
            return;
    }

    /**
     *  Make Url Method userd for Create Custome Url
     * To Sent outside.
     * @param1 token_array [key pair value]
     * @param2 module [Name]
     * @param3 action [Action Name] 
     * @param4 type [authenticate|unauthenticate]
     * @return url
     * @author Anupam Ojha
     */
    public static function makeUrl($token_array = array(), $module = 'admin', $action = 'index', $type = 'authenticate') {
        $url = self::projectUrl() . '/' . $module . '/' . $type . '/' . $action;
        if (!empty($token_array)) {
            $token_array = serialize($token_array);
            $token_encoded_data = base64_encode($token_array);
            $url .= '/token/' . $token_encoded_data;
        }
        return $url;
    }

    /**    Function Name:      isAccess($module='SA',$key='')
     *      Description:        create the folder stucture on the basis of the USERID passed
     *      Parameters:         USERID 
     *      chekiing access rights of current user
     */
    public static function isAccess($module = 'SA', $key = '') {
        //Allow access for 1b with checking given modules and also give access for perticular users.
        $allow_modules = array('REL_ALLOCATOR_ACCESS', 'AUDITOR_ACCESS', 'QA_MANAGER_ACCESS', 'SUBMITTED_SITES');
        $qa_manager_ids = array(1835, 1643, 1838, 1912, 5);
        $auditor_ids = array(471, 1922, 1919, 1918, 1920,1940,5,2006,2007,2009,2075,2365,2364,2398,2396,2394,2391,2395,2392,2393);
        //deleted reviewers 1939 1969 2003 2008
        $id = Yii::app()->session['login']['user_id'];
        if (in_array($module, $allow_modules)) {
            if ($module == 'AUDITOR_ACCESS' && in_array($id, $auditor_ids)) {
                return true;
            } elseif ($module == 'QA_MANAGER_ACCESS' && in_array($id, $qa_manager_ids)) {
                return true;
            } elseif ($module == 'SUBMITTED_SITES' && in_array($id, array(5,33,11))) {
                return true;
            } else {
                return FALSE;
            }
        }

        //Allow access for L2 Switch 1b with checking given modules and also give access for perticular users.
        $allow_access_rights = array('L2_QA_MANAGER', 'L2_QA_REVIEWER', 'AUDITOR_ACCESS', 'QA_MANAGER_ACCESS');
        $l2_qa_manager_ids = array(5, 17, 19, 22, 2650, 1835, 1643, 1838, 1912);
        $l2_reviewer_ids = array(5, 16, 21, 22, 2650, 471, 1922, 1919, 1918, 1920,1940, 2006,2007,2009,2075,2365,2364,2398,2396,2394,2391,2395,2392,2393);
        //deleted reviewers 1939 1969 2003 2008
        if (in_array($module, $allow_access_rights)) {
            if (($module == 'L2_QA_MANAGER' || $module == 'QA_MANAGER_ACCESS' ) && in_array($id, $l2_qa_manager_ids)) {
                return true;
            } elseif (($module == 'L2_QA_REVIEWER' || $module == 'AUDITOR_ACCESS') && in_array($id, $l2_reviewer_ids)) {
                return true;
            } else {
                return FALSE;
            }
        }

        // cheking user role
        if (Yii::app()->session['login']['access_type'] == '1') {
            // If current user is Super Admin
            return true;
        } else {
            // If current user is not a Super Admin
            if (!empty($key)) {
                // chek if user have access to perticuler action
                if (isset(Yii::app()->session['login']['access_rights'][$module]))
                    return (in_array($key, Yii::app()->session['login']['access_rights'][$module]));
                else {
                    return false;
                }
            } else {
                // If key is empty then just return access is available
                return (isset(Yii::app()->session['login']['access_rights'][$module]));
            }
        }
    }

    /**    Function Name:      createFolder($target)
     *      Description:         create the folder stucture on the basis of the USERID passed
     *      Parameters:          USERID 
     *      Creates the folder structure
     */
    function createFolder($target, $flag = false) {
        // from php.net/mkdir user contributed notes
        $target = str_replace('//', '/', $target);
        // safe mode fails with a trailing slash under certain PHP versions.
        $target = rtrim($target, '/'); // Use rtrim() instead of untrailingslashit to avoid formatting.php dependency.
        if (empty($target))
            $target = '/';
        if (file_exists($target))
            return @is_dir($target);
        // Attempting to create the directory may clutter up our display.
        if (@mkdir($target)) {
            $stat = @stat(dirname($target));
            $dir_perms = $stat['mode'] & 0007777;  // Get the permission bits.
            @chmod($target, $dir_perms);
            return true;
        } elseif (is_dir(dirname($target))) {
            return false;
        }

        // If the above failed, attempt to create the parent node, then try again.
        if (( $target != '/' ) && ( self::createFolder(dirname($target)) ))
            return self::createFolder($target);

        return false;
    }

    /**       Function Name :           staticPath($parm,$isCreateFolder = false, $static_path = '')
     *        Description   :           splits user id and returnds the path
     *        Parameters    :           $parm            =   USERID 
     *                                  $isCreateFolder  =   FOLDER TO BE CREATED OR NOT
     *                                  $static_path     =   Base path for usr files
     *        @return the static path
     *    
     */
    function staticPath($parm, $isCreateFolder = false, $static_path = '') {
        $folders = str_split((string) $parm);
        $path = trim($static_path . implode('/', $folders) . '/' . $parm);
        if ($isCreateFolder == true)
            self::createFolder($path);
        return $path;
    }

    /**
     * This method Used for Display PageLimit DropDown Limits
     * @return Array
     * @author Anupam Ojha
     */
    public static function pageLimitDropDown() {
        return array(10 => 10, 20 => 20, 50 => 50, 100 => 100, 100000 => 'All');
    }

    public static function displayErrorSummary($errors = array()) {
        $html = '';
        if (!empty($errors)) {
            $rowIndexs = array_keys($errors);
            $isMultipleRecordsError = (isset($rowIndexs[0]) and is_numeric($rowIndexs[0])) ? true : false;
            $html .= '<div class="errorSummary">';
            $html .= '<p>Please fix the following input errors:</p>';
            if ($isMultipleRecordsError) {
                foreach ($errors as $key => $rowErrors) {
                    $rowNo = ++$key;
                    $html .= '<p>Record #' . $rowNo . ':</p>';
                    $html .= '<ul>';
                    foreach ($rowErrors as $field => $fieldErrors) {
                        foreach ($fieldErrors as $error) {
                            $html .= '<li>' . $error . '</li>';
                        }
                    }
                    $html .= '</ul>';
                }
            } else {
                $html .= '<ul>';
                foreach ($errors as $field => $fieldErrors) {
                    foreach ($fieldErrors as $error) {
                        $html .= '<li>' . $error . '</li>';
                    }
                }
                $html .= '</ul>';
            }
        }
        return $html;
    }

    public static function displaySkippedRecord($skippedRecord = array()) {
        $html = '';
        if (!empty($skippedRecord)) {
            $html .= '<div class="errorSummary">';
            $html .= '<p>Skipping following records:</p>';


            foreach ($skippedRecord AS $record) {
                $html .= '<p>Data Record with ID #' . $record . ' Skipped:</p>';
            }
        } else {
            $html .= '<div class="errorSummary">';
            $html .= '<p>No Record skipped</p>';
        }
        return $html;
    }

    /**
     * This method Used for change date format
     * @return date
     * @author Yogesh Jadhav
     */
    function changeDateTimeFormat($old_date) {

        if ($old_date != '') {
            $arr_date_time = explode(' ', $old_date);
            $arr_date = explode('-', $arr_date_time[0]);
            $new_date = $arr_date[1] . "-" . $arr_date[2] . "-" . $arr_date[0];
            $new_date_time = $new_date . " " . $arr_date_time[1];
        } else {
            $new_date_time = '--';
        }
        return $new_date_time;
    }

    /**
     * This method Used for change date format
     * @return date
     * @author Yogesh Jadhav
     */
    function changeDateFormat($old_date) {
        if ($old_date != '') {
            $arr_date = explode('/', $old_date);
            $new_date = $arr_date[0] . "-" . $arr_date[1] . "-" . $arr_date[2];
        } else {
            $new_date = '--';
        }
        return $new_date;
    }

    /**
     * This method Used for change date format
     * @return date
     * @author Yogesh Jadhav
     */
    function changeDateFormatNew($old_date) {

        if ($old_date != '') {
            $arr_date = explode('-', $old_date);
            $new_date = $arr_date[1] . "/" . $arr_date[2] . "/" . $arr_date[0];
        } else {
            $new_date = '--';
        }
        return $new_date;
    }

    /**
     * This method Used for Check Model access for logged in user
     * Return true if access else false
     * @author Anupam Ojha.
     * Date: 17-Apr-2014. 
     */
    function hasModuleAccess($module = 'estate') {
        $access_type = Yii::app()->session['login']['access_type'];
        if (!isset(Yii::app()->session['login']['user_id']))
            return false;
        switch ($module) {
            case 'admin':
                return in_array($access_type, array('A', 'SA', 'TR'));
            case 'estate':
                return in_array($access_type, array('LFA', 'LFE', 'LFSA', 'TR'));
            case 'lawfirm':
                return in_array($access_type, array('LFA', 'LFSA', 'LFE'));
            default :
                return false;
        }
    }

    /**
     * This method Used to set the page title
     * The format of the title is as follows
     * Application Name:: Module Name - Sub Menu Name - Action Name
     * Renders the page title
     * @author Rakesh Jaware.
     * Date: 17-Apr-2014. 
     */
    function pageTitle() {
        // Get the controller action
        $controller_action = Yii::app()->controller->action->id;
        $page_title = ucwords(Yii::app()->name . ":: ");
        //Get the last element of the Breadcrum array
        $myLastElement = end($this->breadcrumbs);
        //Check that is the controller action is index if so then dont append the controller id tom page title
        if ($controller_action == 'index') {
            $page_title .= ucwords(Yii::app()->controller->module->id . "- " . $myLastElement);
            //echo ucwords(Yii::app()->controller->module->id."-> ".Yii::app()->controller->id);
        } else {
            $page_title .= ucwords(Yii::app()->controller->module->id . "- " . Yii::app()->controller->id . "- " . $myLastElement);
            //echo ucwords(Yii::app()->controller->module->id."-> ".Yii::app()->controller->id."-> ".$controller_action);
        }
        echo $page_title;
    }

    /**    Function Name:      isAccess($module='SA',$key='')
     *      Description:        create the folder stucture on the basis of the USERID passed
     *      Parameters:         USERID 
     *      chekiing access rights of current user
     */
    public static function isLawFirmAdminAccess($module = 'LFE', $key = '') {
        // cheking user role
        if (Yii::app()->session['login']['access_type'] == 'LFA') {
            // If current user is Super Admin
            return true;
        } else {
            // If current user is not a Super Admin
            if (!empty($key)) {
                // chek if user have access to perticuler action
                if (isset(Yii::app()->session['login']['access_rights'][$module]))
                    return (in_array($key, Yii::app()->session['login']['access_rights'][$module]));
                else {
                    return false;
                }
            } else {
                // If key is empty then just return access is available
                return (isset(Yii::app()->session['login']['access_rights'][$module]));
            }
        }
    }

    /**    Function Name:      is_checked($model, $field, $value)
     *      Description:        is checked
     *      Parameters:         $model, $field, $value 
     */
    public static function is_checked($model, $field, $value) {
        if ($model->$field == $value)
            return 'checked';
        else {
            return '';
        }
    }

    /**    Function Name:      isActivePageTab( $pageno, $tabno )
     *      Description:        is active page tab
     *      Parameters:         $pageno, $tabno 
     */
    function isActivePageTab($pageno, $tabno) {
        return ( $pageno == $tabno ) ? 'active' : '';
    }

    /**    Function Name:      getPageUrl( $pageno )
     *      Description:        get page url
     *      Parameters:         $pageno 
     */
    function getPageUrl($pageno) {
        return (!Yii::app()->request->getQuery('pageno') ) ? Yii::app()->request->url . '/page/' . $pageno : $pageno;
    }

    /**    Function Name:      getExplode( $string, $seprator = ' ' )
     *      Description:        get array from string
     *      Parameters:         $string, $seprator 
     */
    function getExplode($string, $seprator = ' ') {
        $characters = explode($seprator, $string);
        $explode_string = array();
        foreach ($characters as $character) {
            $explode_string[] = trim($character);
        }
        return $explode_string;
    }

    public static function calculateAge($date_of_birth, $current_date) {
        $date_of_birth = strtotime($date_of_birth);
        $date_of_current = strtotime($current_date);
        $datediff = $date_of_current - $date_of_birth;
        return floor($datediff / (365 * 60 * 60 * 24));
    }

    /**    Function Name:      in_array_rec( needle, $haystack, $strict = false )
     *      Description:        check value in two dimentional array
     *      Parameters:         $string, $seprator 
     */
    public static function in_array_rec($needle, $haystack, $strict = false) {
        foreach ($haystack as $item) {
            if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && self::in_array_rec($needle, $item, $strict))) {
                return true;
            }
        }
        return false;
    }

    public static function getExistingReasons($model) {
        $modelExistingSiteReasons = AssignedSiteReasonsRel::model()->findAllByAttributes(array('site_id' => $model->id, 'is_active' => true));
        if (count($modelExistingSiteReasons)) {
            $reasons = array();
            foreach ($modelExistingSiteReasons as $objmodelExistingSiteReason) {
                $reasonMaster = SiteReasonsMaster::model()->findByPk($objmodelExistingSiteReason->reason_id);
                $reasons[$objmodelExistingSiteReason->reason_id] = $reasonMaster->reason;
            }
            return array_keys($reasons);
        }
    }

    public static function getBomUploadUrl($absUrl = false) {
        if (!$absUrl) {
            return yii::app()->baseUrl . '/uploads/bomtemplates/';
        }
        return dirname(Yii::app()->basePath) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'bomtemplates' . DIRECTORY_SEPARATOR;
        ;
    }

    /**
     * Shared environment safe version of mkdir. Supports recursive creation.
     * For avoidance of umask side-effects chmod is used.
     *
     * @param string $dst path to be created
     * @param integer $mode the permission to be set for newly created directories, if not set - 0777 will be used
     * @param boolean $recursive whether to create directory structure recursive if parent dirs do not exist
     * @return boolean result of mkdir
     * @see mkdir
     */
    public static function createDirectory($dst, $mode = null, $recursive = false) {
        if ($mode === null)
            $mode = 0777;
        $prevDir = dirname($dst);
        if ($recursive && !is_dir($dst) && !is_dir($prevDir))
            self::createDirectory(dirname($dst), $mode, true);
        $res = mkdir($dst, $mode);
        @chmod($dst, $mode);
        return $res;
    }

    public static function downloadFiles($fullFilePath, $unlink = false) {

        // send $filename to browser
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $fullFilePath);
        $size = filesize($fullFilePath);
        $name = basename($fullFilePath);
        //CHelper::debug($mimeType);
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            // cache settings for IE6 on HTTPS
            header('Cache-Control: max-age=120');
            header('Pragma: public');
        } else {
            header('Cache-Control: private, max-age=120, must-revalidate');
            header("Pragma: no-cache");
        }
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // long ago
        header("Content-Type: $mimeType");
        header('Content-Disposition: attachment; filename="' . $name . '";');
        header("Accept-Ranges: bytes");
        header('Content-Length: ' . filesize($fullFilePath));
        ob_clean();
        flush();
        print readfile($fullFilePath);

        if ($unlink)
            unlink($fullFilePath);

        exit;
    }

    public static function downloadFilesNew($fullFilePath, $unlink = false) {

        ob_clean();
        $file_for_user = basename($fullFilePath);
        $full_path_file = $fullFilePath;
        header("Content-type: application/octet-stream");
        header('Content-Disposition: attachment; filename="' . $file_for_user . '"');
        readfile($full_path_file);
        exit();
    }

    public static function removeSplChar($str) {
        return preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $str);
    }

    public static function customDateFormat($date) {
        if (!empty($date) && $date != "0000-00-00 00:00:00") {

            $date = str_replace("/", "-", $date);
            return date("Y-m-d H:i:s", strtotime($date));
        }

        return "---";
    }

    public static function encodeParams($params) {
        $token_array = serialize($params);
        $token_encoded_data = base64_encode($token_array);
        return $token_encoded_data;
    }

    /**
     * @author Rishikesh Jadhav.
     * @purpose To export CArrayDataProvider grid result into csv.
     * @param type $grid_result => Mandatory : dataprovider result
     * @param type $columns => Optional : Array of column names which u want to export in csv, with key value format. eg: array('first_name' => 'First Name','cnt'=>'Count'). By default all columns which present in grid
     * @param type $filename => Optional : File name which u want to give for downloadable file. By default 'template.csv'
     */
    public static function exportGridCArrayDataProvider($grid_result, $columns = array(), $filename = "template.csv") {
        if (!empty($grid_result->rawData)) {
            $outString = "";
            if (!empty($columns)) {
                $columns = array_intersect_key($columns, $grid_result->rawData[0]);
                $outString .= strip_tags(implode(",", $columns)) . "\n";
            } else {
                $outString .= strip_tags(implode(",", array_keys($grid_result->rawData[0]))) . "\n";
            }
            foreach ($grid_result->rawData as $rows) {
                if (!empty($columns)) {
                    $rows = array_intersect_key($rows, $columns);
                }
                $outString .= strip_tags(implode(",", $rows)) . "\n";
            }
            ob_clean();
            header('Content-type: application/csv');
            header('Content-Disposition: attachment; filename=' . $filename);
            echo $outString;
            exit;
        }
    }

}
