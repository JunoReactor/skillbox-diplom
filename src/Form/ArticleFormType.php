<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Article;

use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ArticleFormType extends AbstractType
{
    private $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        /** Article|null $article */
        $article = $options['data'] ?? null;

        
        if(! $article || $article->getImageFilename() ){
 
        }

        $builder
            ->add('title')
            ->add('body')
            ->add('description')
            ->add('keywords')
            ->add('publishedAt', null, [
                'widget' => 'single_text',
            ])
            ->add('author', EntityType::class, [
                'class'         => User::class,
                'choice_label'  => function(User $user) {
                    return sprintf('%s (id: %d)', $user->getFirstName(), $user->getId() );
                },
                'placeholder' => 'Выберите автора статьи',
                'choices' => $this->userRepository->findAllSortedByName(),
            ]);

        $builder->get('body')
                ->addModelTransformer(new CallbackTransformer(
                    function ($bodyFromDatabase) {
                        return str_replace('**собака**', 'собака',$bodyFromDatabase);
                    },
                    function ($bodyFromInput) {
                        return str_replace('собака', '**собака**',$bodyFromInput);
                    }
                ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
