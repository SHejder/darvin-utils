# darvin-utils
A set of useful utilities:

- [**Cloner**](/Resources/doc/cloner.md) - сервис для клонирования сущностей;
- [**Абстрактный класс консольной команды**](/Command/AbstractContainerAwareCommand.php) - класс, добавляющий методы для
удобного вывода сообщений;
- [**Custom entity loader**](/Resources/doc/custom_entity_loader.md) - сервис для инициализации одной сущности в поле
другой, при этом информация для инициализации (класс, название свойства, значение свойства) берется из свойств последней;
- [**Default value**](/Resources/doc/default_value.md) - функционал, который позволяет использовать значение одного свойства
сущности в качестве значения по умолчанию другого свойства;
- [**Config injector**](/Resources/doc/config_injector.md) - класс для инжекта конфигурации в DI-контейнер в виде набора параметров.
