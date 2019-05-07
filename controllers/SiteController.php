<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

use app\models\Author;
use app\models\Book;
use app\models\Shop;
use yii\helpers\ArrayHelper;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        if( Yii::$app->request->isAjax){
            $arrayBooks = Yii::$app->request->post("Book");  // получаем список id выбранных книг 
            $arrayAuthors = Yii::$app->request->post("Author");  // получаем список id выбранных авторов           
            $arrayShops = Yii::$app->request->post("Shop"); // получаем список id выбранных магазинов

            $books_array = array(); // создаем пустой массив для заполнения id книг

            if(is_array($arrayBooks['name']) && count($arrayBooks['name'])>0){
                array_push($books_array, $arrayBooks['name']);
            }

            if(is_array($arrayAuthors['name']) && count($arrayAuthors['name'])>0){  
               // выбираем из таблицы связей книг по автору
                $rows = (new \yii\db\Query())
                        ->select(['id_book'])
                        ->from('book_author_link')
                        ->where(['id_author' => $arrayAuthors['name']])
                        ->groupBy(['id_book'])
                        ->all(); 

                array_push($books_array, array_column($rows,'id_book')); // вставляем id найденных книг
            }

            if(is_array($arrayShops['name']) && count($arrayShops['name'])>0){ 
            // выбираем из таблицы связей книг по магазинам  
                $rows = (new \yii\db\Query())
                        ->select(['id_book'])
                        ->from('book_shop_link')
                        ->where(['id_shop' => $arrayShops['name']])
                        ->groupBy(['id_book'])
                        ->all(); 
                        
                array_push($books_array, array_column($rows,'id_book')); // вставляем id найденных книг
            }

            $ids_product_list = array();
            foreach ($books_array as $v) {
                    foreach ($v as $vv) {
                          $ids_product_list[] = $vv;
                      }  
                }  
            $ids_product_list = implode(',',array_unique($ids_product_list)); // унифицируем массив и разбиваем в строку для запроса

            $books_ajax = array();
            if(!empty($ids_product_list)){
                        // получаем книги с прикрепленными авторами по id-шникам
                        $query = 'SELECT 
                                  *,
                                  (SELECT 
                                    GROUP_CONCAT(`authors`.`name` SEPARATOR \' \') 
                                  FROM
                                    `book_author_link` 
                                    JOIN
                                    `authors` 
                                    ON `authors`.`id` = book_author_link.id_author 
                                  WHERE book_author_link.id_book = books.id) AS author_c,
                                  (SELECT 
                                    GROUP_CONCAT(`shops`.`name` SEPARATOR \', \') 
                                  FROM
                                    `book_shop_link` 
                                    JOIN
                                    `shops` 
                                    ON `shops`.`id` = book_shop_link.id_shop 
                                  WHERE book_shop_link.id_book = books.id) AS shop_c 
                                FROM
                                  books 
                                WHERE books.id IN ('.$ids_product_list.');';
                        $books_ajax = Book::findBySql($query)->asArray()->all();
            }
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            
            return $books_ajax;
            
        }

        $authors = ArrayHelper::map( Author::find()->all(),'id','name');
        //$books = Book::find()->asArray()->all();
     
        $books = ArrayHelper::map( Book::find()->all(),'id','name');
       
        $shops = ArrayHelper::map( Shop::find()->all(),'id','name');

        return $this->render('index', compact(['authors','books','shops']));
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
